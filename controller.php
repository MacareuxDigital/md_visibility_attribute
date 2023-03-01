<?php

namespace Concrete\Package\MdVisibilityAttribute;

use Concrete\Core\Attribute\Category\CategoryService;
use Concrete\Core\Attribute\TypeFactory;
use Concrete\Core\Cache\Level\RequestCache;
use Concrete\Core\Entity\Attribute\Category;
use Concrete\Core\Package\Package;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Type\Event;
use Concrete\Core\Permission\Access\Entity\GroupEntity;
use Concrete\Core\Permission\Key\PageKey as PagePermissionKey;
use Concrete\Core\User\Group\Group;
use Macareux\VisibilityAttribute\Entity\VisibilityAttributeValue;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Concrete\Core\Permission\Access\Access as PermissionAccess;

class Controller extends Package
{
    protected $pkgHandle = 'md_visibility_attribute';
    protected $appVersionRequired = '8.5.5';
    protected $pkgVersion = '0.1.0';
    protected $pkgAutoloaderRegistries = [
        'src/Entity' => '\Macareux\VisibilityAttribute\Entity',
    ];

    public function getPackageName()
    {
        return t('Macareux Visibility Control Attribute');
    }

    public function getPackageDescription()
    {
        return t('Add an attribute type to control visibility of pages.');
    }

    public function install()
    {
        $pkg = parent::install();

        /** @var TypeFactory $factory */
        $factory = $this->app->make(TypeFactory::class);
        $type = $factory->getByHandle('visibility');
        if (!is_object($type)) {
            $type = $factory->add('visibility', 'Visibility', $pkg);
            /** @var CategoryService $service */
            $service = $this->app->make(CategoryService::class);
            $category = $service->getByHandle('collection')->getController();
            $category->associateAttributeKeyType($type);
        }

        return $pkg;
    }

    public function on_start()
    {
        /** @var RequestCache $requestCache */
        $requestCache = $this->app->make('cache/request');
        /** @var EventDispatcherInterface $director */
        $director = $this->app->make('director');
        $director->addListener('on_page_type_publish', function ($event) use ($requestCache) {
            $requestCache->disable();
            /** @var Event $event */
            $c = $event->getPageObject();
            /** @var CategoryService $service */
            $service = $this->app->make(CategoryService::class);
            /** @var Category $category */
            $category = $service->getByHandle('collection');
            $attributes = $category->getController()->getList();
            foreach ($attributes as $attribute) {
                if ($attribute->getAttributeTypeHandle() === 'visibility') {
                    /** @var VisibilityAttributeValue $visibility */
                    $visibility = $c->getAttribute($attribute->getAttributeKeyHandle());
                    if ($visibility) {
                        $visibleGroups = $visibility->getVisibleGroups();
                        self::setVisibleGroups($c, $visibleGroups);
                    }
                    break;
                }
            }
        });
    }

    protected static function setVisibleGroups(Page $page, array $groupIDs): void
    {
        if ($page->getCollectionInheritance() !== 'OVERRIDE') {
            $page->setPermissionsToManualOverride();
        }
        $pk = PagePermissionKey::getByHandle('view_page');
        if ($pk) {
            $pk->setPermissionObject($page);
            $pt = $pk->getPermissionAssignmentObject();
            $pa = $pk->getPermissionAccessObject();
            if (!$pa) {
                $pa = PermissionAccess::create($pk);
            } elseif ($pa->isPermissionAccessInUse()) {
                $pa = $pa->duplicate();
            }
            foreach ($pa->getAccessListItems() as $accessListItem) {
                $accessEntity = $accessListItem->getAccessEntityObject();
                if ($accessEntity->getAccessEntityTypeHandle() === 'group') {
                    /** @var Group|null $group */
                    $group = $accessEntity->getGroupObject();
                    if ($group) {
                        $groupID = $group->getGroupID();
                        if ($groupID !== ADMIN_GROUP_ID && (!in_array($groupID, $groupIDs) || empty($groupIDs))) {
                            core_log(sprintf('Remove access: Page %s, Group %s', $page->getCollectionPath(), $group->getGroupDisplayName(false)));
                            $pa->removeListItem($accessEntity);
                        }
                    }
                }
            }
            foreach ($groupIDs as $groupID) {
                $group = Group::getByID($groupID);
                if ($group) {
                    $pe = GroupEntity::getOrCreate($group);
                    core_log(sprintf('Add access: Page %s, Group %s', $page->getCollectionPath(), $group->getGroupDisplayName(false)));
                    $pa->addListItem($pe);
                }
            }
            $pa->markAsInUse();
            $pt->assignPermissionAccess($pa);
        }
    }
}

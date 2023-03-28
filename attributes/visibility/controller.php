<?php

namespace Concrete\Package\MdVisibilityAttribute\Attribute\Visibility;

use Concrete\Core\Attribute\FontAwesomeIconFormatter;
use Concrete\Core\Permission\Access\Entity\GroupEntity;
use Concrete\Core\User\Group\Group;
use Concrete\Core\User\Group\GroupList;
use Macareux\VisibilityAttribute\Entity\VisibilityAttributeSettings;
use Macareux\VisibilityAttribute\Entity\VisibilityAttributeValue;
use Concrete\Core\Permission\Key\PageKey as PagePermissionKey;

class Controller extends \Concrete\Core\Attribute\Controller
{
    public $helpers = ['form'];

    /** @var bool */
    protected $visibility = true;

    protected $optionGroups = [];
    protected $allowMultiple = false;

    public function form()
    {
        $this->load();

        if (is_object($this->attributeValue)) {
            /** @var VisibilityAttributeValue $value */
            $value = $this->getAttributeValue()->getValue();
            if ($value) {
                $this->set('visibleGroups', $value->getVisibleGroups());
            }
        } else {
            $visibleGroups = [];
            $c = $this->request->getCurrentPage();
            if ($c) {
                $pk = PagePermissionKey::getByHandle('view_page');
                $pk->setPermissionObject($c);
                $pa = $pk->getPermissionAccessObject();
                if ($pa) {
                    $groupList = new GroupList();
                    $groupList->includeAllGroups();
                    foreach ($groupList->getResults() as $group) {
                        $pe = GroupEntity::getOrCreate($group);
                        if ($pa->validateAccessEntities([$pe])) {
                            $visibleGroups[] = $group->getGroupID();
                        }
                    }
                }
            }
            $this->set('visibleGroups', $visibleGroups);
        }
        if (is_object($this->getAttributeKeySettings())) {
            /** @var VisibilityAttributeSettings $settings */
            $settings = $this->getAttributeKeySettings();
            $optionGroups = [];
            foreach ($settings->getOptionGroups() as $gID) {
                if ($gID !== ADMIN_GROUP_ID) {
                    $optionGroup = Group::getByID($gID);
                    if ($optionGroup) {
                        $optionGroups[] = [
                            'id' => $gID,
                            'label' => $optionGroup->getGroupDisplayName(false, false)
                        ];
                    }
                }
            }
            $this->set('optionGroups', $optionGroups);
            $this->set('allowMultiple', $settings->isAllowMultiple());
        }

        $this->set('key', $this->attributeKey);
        $this->set('akID', $this->attributeKey->getAttributeKeyID());
        $this->requireAsset('selectize');
    }

    public function getIconFormatter()
    {
        return new FontAwesomeIconFormatter('key');
    }

    public function getAttributeValueClass()
    {
        return VisibilityAttributeValue::class;
    }

    public function getAttributeValueObject()
    {
        return $this->attributeValue ? $this->entityManager->find(VisibilityAttributeValue::class, $this->attributeValue->getGenericValue()) : null;
    }

    public function createAttributeValue($data)
    {
        if ($data instanceof VisibilityAttributeValue) {
            return clone $data;
        }
        $av = new VisibilityAttributeValue();
        $av->setVisibleGroups((array)$data);

        return $av;
    }

    public function createAttributeValueFromRequest()
    {
        $data = $this->post();
        return $this->createAttributeValue(explode(',', $data['visibleGroups']));
    }

    public function validateForm($data)
    {
        if (empty($data['visibleGroups'])) {
            return false;
        }

        return true;
    }

    public function validateValue()
    {
        return $this->attributeValue->getValue() !== '';
    }

    public function type_form()
    {
        $this->load();

        $availableGroups = [];
        $groupList = new GroupList();
        $groupList->includeAllGroups();
        /** @var Group $group */
        foreach ($groupList->getResults() as $group) {
            $availableGroups[$group->getGroupID()] = $group->getGroupDisplayName(false, true);
        }

        $this->set('availableGroups', $availableGroups);
    }

    public function getAttributeKeySettingsClass()
    {
        return VisibilityAttributeSettings::class;
    }

    public function saveKey($data)
    {
        /**
         * @var VisibilityAttributeSettings $type
         */
        $type = $this->getAttributeKeySettings();
        $data += [
            'optionGroups' => $data['optionGroups'],
            'allowMultiple' => $data['allowMultiple']
        ];
        $optionGroups = (array)$data['optionGroups'];
        $allowMultiple = (bool)$data['allowMultiple'];
        $type->setOptionGroups($optionGroups);
        $type->setAllowMultiple($allowMultiple);

        return $type;
    }

    public function load()
    {
        $ak = $this->getAttributeKey();
        if (!is_object($ak)) {
            return false;
        }

        /**
         * @var VisibilityAttributeSettings $type
         */
        $type = $this->getAttributeKeySettings();
        $optionGroups = $type->getOptionGroups();
        $allowMultiple = $type->isAllowMultiple();
        $this->optionGroups = $optionGroups;
        $this->allowMultiple = $allowMultiple;
        $this->set('optionGroups', $optionGroups);
        $this->set('allowMultiple', $allowMultiple);
    }
}

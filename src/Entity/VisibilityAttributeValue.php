<?php

namespace Macareux\VisibilityAttribute\Entity;

use Concrete\Core\Entity\Attribute\Value\Value\AbstractValue;
use Concrete\Core\User\Group\Group;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="atVisibilityAttributeValue")
 */
class VisibilityAttributeValue extends AbstractValue
{
    /**
     * @ORM\Column(type="json_array")
     */
    protected $visibleGroups = [];

    /**
     * @return array
     */
    public function getVisibleGroups(): array
    {
        return $this->visibleGroups;
    }

    /**
     * @param array $visibleGroups
     */
    public function setVisibleGroups(array $visibleGroups): void
    {
        $this->visibleGroups = $visibleGroups;
    }

    public function __toString()
    {
        $groupNames = [];
        foreach ($this->getVisibleGroups() as $groupID) {
            $group = Group::getByID($groupID);
            if ($group) {
                $groupNames[] = $group->getGroupDisplayName(false, false);
            }
        }

        return implode(', ', $groupNames);
    }
}
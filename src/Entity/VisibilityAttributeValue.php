<?php

namespace Macareux\VisibilityAttribute\Entity;

use Concrete\Core\Entity\Attribute\Value\Value\AbstractValue;
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
}
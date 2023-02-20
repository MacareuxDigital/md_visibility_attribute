<?php

namespace Macareux\VisibilityAttribute\Entity;

use Concrete\Core\Entity\Attribute\Key\Settings\Settings;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 * @ORM\Table(name="atVisibilityAttributeSettings")
 */
class VisibilityAttributeSettings extends Settings
{
    /**
     * @ORM\Column(type="json_array")
     */
    protected $optionGroups = [];

    /**
     * @ORM\Column(type="boolean")
     */
    protected $allowMultiple = false;

    /**
     * @return array
     */
    public function getOptionGroups(): array
    {
        return $this->optionGroups;
    }

    /**
     * @param array $optionGroups
     */
    public function setOptionGroups(array $optionGroups): void
    {
        $this->optionGroups = $optionGroups;
    }

    /**
     * @return bool
     */
    public function isAllowMultiple(): bool
    {
        return $this->allowMultiple;
    }

    /**
     * @param bool $allowMultiple
     */
    public function setAllowMultiple(bool $allowMultiple): void
    {
        $this->allowMultiple = $allowMultiple;
    }
}
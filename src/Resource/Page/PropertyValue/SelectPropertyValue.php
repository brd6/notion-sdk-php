<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Page\PropertyValue;

use Brd6\NotionSdkPhp\Resource\Property\SelectProperty;

class SelectPropertyValue extends AbstractPropertyValue
{
    protected ?SelectProperty $select = null;

    protected function initialize(): void
    {
        $data = (array) $this->getRawData()[$this->getType()];
        $this->select = SelectProperty::fromRawData($data);
    }

    public function getSelect(): ?SelectProperty
    {
        return $this->select;
    }

    public function setSelect(?SelectProperty $select): self
    {
        $this->select = $select;

        return $this;
    }
}

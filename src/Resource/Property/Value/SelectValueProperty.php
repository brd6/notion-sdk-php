<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Property\Value;

use Brd6\NotionSdkPhp\Resource\Property\SelectProperty;

class SelectValueProperty extends AbstractValueProperty
{
    protected ?SelectProperty $select = null;

    protected function initialize(): void
    {
        $data = (array) $this->getRawData()['select'];
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

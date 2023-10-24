<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Page\PropertyValue;

use Brd6\NotionSdkPhp\Resource\Property\SelectProperty;

class StatusPropertyValue extends AbstractPropertyValue
{
    protected ?SelectProperty $status = null;

    protected function initialize(): void
    {
        $data = (array) $this->getRawData()[$this->getType()];
        $this->status = SelectProperty::fromRawData($data);
    }

    public function getStatus(): ?SelectProperty
    {
        return $this->status;
    }

    public function setStatus(?SelectProperty $status): self
    {
        $this->status = $status;

        return $this;
    }
}

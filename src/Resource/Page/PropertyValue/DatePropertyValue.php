<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Page\PropertyValue;

use Brd6\NotionSdkPhp\Resource\Page\AbstractPropertyValue;
use Brd6\NotionSdkPhp\Resource\Property\DateProperty;

class DatePropertyValue extends AbstractPropertyValue
{
    protected ?DateProperty $date = null;

    protected function initialize(): void
    {
        $data = (array) $this->getRawData()[$this->getType()];
        $this->date = DateProperty::fromRawData($data);
    }

    public function getDate(): ?DateProperty
    {
        return $this->date;
    }

    public function setDate(?DateProperty $date): self
    {
        $this->date = $date;

        return $this;
    }
}

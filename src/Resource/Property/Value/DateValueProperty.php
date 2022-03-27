<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Property\Value;

use Brd6\NotionSdkPhp\Resource\Property\DateProperty;

class DateValueProperty extends AbstractValueProperty
{
    protected ?DateProperty $date = null;

    protected function initialize(): void
    {
        $this->date = isset($this->getRawData()['date']) ?
            DateProperty::fromRawData((array) $this->getRawData()['date']) :
            null;
    }

    public function getDate(): ?DateProperty
    {
        return $this->date;
    }

    public function setDate(DateProperty $date): self
    {
        $this->date = $date;

        return $this;
    }
}

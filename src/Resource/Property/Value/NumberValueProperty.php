<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Property\Value;

class NumberValueProperty extends AbstractValueProperty
{
    protected ?float $number = null;

    protected function initialize(): void
    {
        $this->number = isset($this->getRawData()['number']) ?
            (float) $this->getRawData()['number'] :
            null;
    }

    public function getNumber(): ?float
    {
        return $this->number;
    }

    public function setNumber(float $number): self
    {
        $this->number = $number;

        return $this;
    }
}

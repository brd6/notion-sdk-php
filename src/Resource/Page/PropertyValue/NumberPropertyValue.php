<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Page\PropertyValue;

class NumberPropertyValue extends AbstractPropertyValue
{
    protected ?float $number = null;

    protected function initialize(): void
    {
        $data = $this->getRawData();
        $this->number = isset($data['number']) ? (float) $data['number'] : null;
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

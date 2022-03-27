<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Property\Formula;

class NumberFormulaProperty extends AbstractFormulaProperty
{
    protected ?int $number = null;

    protected function initialize(): void
    {
        $this->number = isset($this->getRawData()['number']) ?
            (int) $this->getRawData()['number'] :
            null;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(int $number): self
    {
        $this->number = $number;

        return $this;
    }
}

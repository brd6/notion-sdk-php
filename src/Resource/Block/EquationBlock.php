<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Block;

use Brd6\NotionSdkPhp\Resource\Property\EquationProperty;

class EquationBlock extends AbstractBlock
{
    protected ?EquationProperty $equation = null;

    protected function initializeBlockProperty(): void
    {
        $data = (array) $this->getRawData()[$this->getType()];
        $this->equation = EquationProperty::fromRawData($data);
    }

    public function getEquation(): ?EquationProperty
    {
        return $this->equation;
    }

    public function setEquation(?EquationProperty $equation): EquationBlock
    {
        $this->equation = $equation;

        return $this;
    }
}

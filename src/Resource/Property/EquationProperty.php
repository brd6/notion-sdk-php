<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Property;

use Brd6\NotionSdkPhp\Resource\AbstractProperty;

class EquationProperty extends AbstractProperty
{
    protected string $expression = '';

    public static function fromRawData(array $rawData): self
    {
        $property = new self();

        $property->expression = (string) $rawData['expression'];

        return $property;
    }

    public function getExpression(): string
    {
        return $this->expression;
    }

    public function setExpression(string $expression): self
    {
        $this->expression = $expression;

        return $this;
    }
}

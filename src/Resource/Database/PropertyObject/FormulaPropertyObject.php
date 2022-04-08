<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Database\PropertyObject;

use Brd6\NotionSdkPhp\Resource\Database\PropertyConfiguration\FormulaPropertyConfiguration;

class FormulaPropertyObject extends AbstractPropertyObject
{
    protected ?FormulaPropertyConfiguration $expression = null;

    public function __construct()
    {
        $this->expression = new FormulaPropertyConfiguration();
    }

    protected function initialize(): void
    {
        $data = (array) $this->getRawData()[$this->getType()];
        $this->expression = FormulaPropertyConfiguration::fromRawData($data);
    }

    public function getExpression(): ?FormulaPropertyConfiguration
    {
        return $this->expression;
    }

    public function setExpression(FormulaPropertyConfiguration $expression): self
    {
        $this->expression = $expression;

        return $this;
    }
}

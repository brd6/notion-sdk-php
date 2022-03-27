<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Page\PropertyValue;

use Brd6\NotionSdkPhp\Resource\Page\AbstractPropertyValue;
use Brd6\NotionSdkPhp\Resource\Property\Formula\AbstractFormulaProperty;

class FormulaPropertyValue extends AbstractPropertyValue
{
    protected ?AbstractFormulaProperty $formula = null;

    protected function initialize(): void
    {
        $data = (array) $this->getRawData()[$this->getType()];
        $this->formula = AbstractFormulaProperty::fromRawData($data);
    }

    public function getFormula(): ?AbstractFormulaProperty
    {
        return $this->formula;
    }

    public function setFormula(?AbstractFormulaProperty $formula): self
    {
        $this->formula = $formula;

        return $this;
    }
}

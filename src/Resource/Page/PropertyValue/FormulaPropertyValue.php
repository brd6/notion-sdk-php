<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Page\PropertyValue;

use Brd6\NotionSdkPhp\Resource\Property\Value\AbstractValueProperty;

class FormulaPropertyValue extends AbstractPropertyValue
{
    protected ?AbstractValueProperty $formula = null;

    protected function initialize(): void
    {
        $data = (array) $this->getRawData()[$this->getType()];
        $this->formula = AbstractValueProperty::fromRawData($data);
    }

    public function getFormula(): ?AbstractValueProperty
    {
        return $this->formula;
    }

    public function setFormula(?AbstractValueProperty $formula): self
    {
        $this->formula = $formula;

        return $this;
    }
}

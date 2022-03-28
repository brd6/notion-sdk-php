<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Page\PropertyValue;

use Brd6\NotionSdkPhp\Resource\Property\Value\AbstractValueProperty;

class RollupPropertyValue extends AbstractPropertyValue
{
    protected ?AbstractValueProperty $rollup = null;

    protected function initialize(): void
    {
        $data = (array) $this->getRawData()[$this->getType()];
        $this->rollup = AbstractValueProperty::fromRawData($data);
    }

    public function getRollup(): ?AbstractValueProperty
    {
        return $this->rollup;
    }

    public function setRollup(?AbstractValueProperty $rollup): self
    {
        $this->rollup = $rollup;

        return $this;
    }
}

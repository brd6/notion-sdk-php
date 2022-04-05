<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Page\PropertyValue;

use Brd6\NotionSdkPhp\Exception\InvalidPropertyException;
use Brd6\NotionSdkPhp\Exception\UnsupportedPropertyTypeException;
use Brd6\NotionSdkPhp\Resource\Property\Value\AbstractValueProperty;

class RollupPropertyValue extends AbstractPropertyValue
{
    protected ?AbstractValueProperty $rollup = null;

    /**
     * @throws InvalidPropertyException
     * @throws UnsupportedPropertyTypeException
     */
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

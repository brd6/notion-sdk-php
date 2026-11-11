<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Property\Value;

use Brd6\NotionSdkPhp\Exception\InvalidPropertyException;
use Brd6\NotionSdkPhp\Exception\UnsupportedPropertyValueException;
use Brd6\NotionSdkPhp\Resource\Page\PropertyValue\AbstractPropertyValue;
use Brd6\NotionSdkPhp\Resource\Property\AbstractProperty;

use function array_map;

class ArrayValueProperty extends AbstractValueProperty
{
    /**
     * @var array<AbstractValueProperty|AbstractPropertyValue>
     */
    protected array $array = [];

    /**
     * @throws InvalidPropertyException
     */
    protected function initialize(): void
    {
        $this->array = array_map(
            fn (array $rawData) => $this->hydrateItem($rawData),
            (array) $this->getRawData()['array'],
        );
    }

    /**
     * @return array<AbstractValueProperty|AbstractPropertyValue>
     */
    public function getArray(): array
    {
        return $this->array;
    }

    /**
     * @param array<AbstractValueProperty|AbstractPropertyValue> $array
     */
    public function setArray(array $array): self
    {
        $this->array = $array;

        return $this;
    }

    /**
     * @return AbstractValueProperty|AbstractPropertyValue
     *
     * @throws InvalidPropertyException
     */
    private function hydrateItem(array $rawData): AbstractProperty
    {
        $value = AbstractValueProperty::fromRawData($rawData);

        if (!$value instanceof UnsupportedValueProperty) {
            return $value;
        }

        try {
            return AbstractPropertyValue::fromRawData($rawData);
        } catch (UnsupportedPropertyValueException $exception) {
            return $value;
        }
    }
}

<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Property\Value;

use Brd6\NotionSdkPhp\Exception\InvalidPropertyException;
use Brd6\NotionSdkPhp\Exception\UnsupportedPropertyTypeException;

use function array_map;

class ArrayValueProperty extends AbstractValueProperty
{
    /**
     * @var array|AbstractValueProperty[]
     */
    protected array $array = [];

    /**
     * @throws InvalidPropertyException
     * @throws UnsupportedPropertyTypeException
     */
    protected function initialize(): void
    {
        $this->array = array_map(
            fn (array $rawData) => AbstractValueProperty::fromRawData($rawData),
            (array) $this->getRawData()['array'],
        );
    }

    /**
     * @return array|AbstractValueProperty[]
     */
    public function getArray(): array
    {
        return $this->array;
    }

    /**
     * @param array|AbstractValueProperty[] $array
     */
    public function setArray(array $array): self
    {
        $this->array = $array;

        return $this;
    }
}

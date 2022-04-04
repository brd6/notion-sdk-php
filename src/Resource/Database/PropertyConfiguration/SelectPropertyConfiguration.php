<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Database\PropertyConfiguration;

use Brd6\NotionSdkPhp\Resource\Property\AbstractProperty;
use Brd6\NotionSdkPhp\Resource\Property\SelectProperty;

use function array_map;

class SelectPropertyConfiguration extends AbstractProperty
{
    /**
     * @var array|SelectProperty[]
     */
    protected array $options = [];

    public static function fromRawData(array $rawData): self
    {
        $property = new self();

        $data = (array) $rawData['options'];
        $property->options = array_map(fn (array $selectData) => SelectProperty::fromRawData($selectData), $data);

        return $property;
    }

    /**
     * @return array|SelectProperty[]
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param array|SelectProperty[] $options
     */
    public function setOptions(array $options): self
    {
        $this->options = $options;

        return $this;
    }
}

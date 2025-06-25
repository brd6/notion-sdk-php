<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Property\Value;

use Brd6\NotionSdkPhp\Resource\Property\SelectProperty;

use function array_map;

class MultiSelectValueProperty extends AbstractValueProperty
{
    /**
     * @var array|SelectProperty[]
     */
    protected array $multiSelect = [];

    protected function initialize(): void
    {
        $data = (array) $this->getRawData()['multi_select'];
        $this->multiSelect = array_map(
            fn (array $selectData) => SelectProperty::fromRawData($selectData),
            $data,
        );
    }

    /**
     * @return array|SelectProperty[]
     */
    public function getMultiSelect(): array
    {
        return $this->multiSelect;
    }

    /**
     * @param array|SelectProperty[] $multiSelect
     */
    public function setMultiSelect(array $multiSelect): self
    {
        $this->multiSelect = $multiSelect;

        return $this;
    }
}

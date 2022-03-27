<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Page\PropertyValue;

use Brd6\NotionSdkPhp\Resource\Page\AbstractPropertyValue;
use Brd6\NotionSdkPhp\Resource\Property\SelectProperty;

use function array_map;

class MultiSelectPropertyValue extends AbstractPropertyValue
{
    /**
     * @var array|SelectProperty[]
     */
    protected array $multiSelect = [];

    protected function initialize(): void
    {
        $data = (array) $this->getRawData()[$this->getType()];
        $this->multiSelect = array_map(fn (array $selectData) => SelectProperty::fromRawData($selectData), $data);
    }

    /**
     * @return array|SelectProperty[]
     */
    public function getMultiSelect(): array
    {
        return $this->multiSelect;
    }

    public function setMultiSelect(array $multiSelect): self
    {
        $this->multiSelect = $multiSelect;

        return $this;
    }
}

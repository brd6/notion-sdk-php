<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Database\PropertyObject;

use Brd6\NotionSdkPhp\Resource\Database\PropertyConfiguration\SelectPropertyConfiguration;

class MultiSelectPropertyObject extends AbstractPropertyObject
{
    protected ?SelectPropertyConfiguration $multiSelect = null;

    public function __construct()
    {
        $this->multiSelect = new SelectPropertyConfiguration();
    }

    protected function initialize(): void
    {
        $data = (array) $this->getRawData()[$this->getType()];
        $this->multiSelect = SelectPropertyConfiguration::fromRawData($data);
    }

    public function getMultiSelect(): ?SelectPropertyConfiguration
    {
        return $this->multiSelect;
    }

    public function setMultiSelect(?SelectPropertyConfiguration $multiSelect): self
    {
        $this->multiSelect = $multiSelect;

        return $this;
    }
}

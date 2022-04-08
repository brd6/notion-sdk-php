<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Database\PropertyObject;

use Brd6\NotionSdkPhp\Resource\Database\PropertyConfiguration\SelectPropertyConfiguration;

class SelectPropertyObject extends AbstractPropertyObject
{
    protected ?SelectPropertyConfiguration $select = null;

    public function __construct()
    {
        $this->select = new SelectPropertyConfiguration();
    }

    protected function initialize(): void
    {
        $data = (array) $this->getRawData()[$this->getType()];
        $this->select = SelectPropertyConfiguration::fromRawData($data);
    }

    public function getSelect(): ?SelectPropertyConfiguration
    {
        return $this->select;
    }

    public function setSelect(?SelectPropertyConfiguration $select): self
    {
        $this->select = $select;

        return $this;
    }
}

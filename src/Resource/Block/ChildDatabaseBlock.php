<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Block;

use Brd6\NotionSdkPhp\Resource\AbstractBlock;
use Brd6\NotionSdkPhp\Resource\Property\ChildDatabaseProperty;

class ChildDatabaseBlock extends AbstractBlock
{
    protected ?ChildDatabaseProperty $childDatabase = null;

    protected function initializeBlockProperty(): void
    {
        $data = (array) $this->getRawData()[$this->getType()];
        $this->childDatabase = ChildDatabaseProperty::fromRawData($data);
    }

    public function getChildDatabase(): ?ChildDatabaseProperty
    {
        return $this->childDatabase;
    }

    /**
     * @param ChildDatabaseProperty $childDatabase
     *
     * @return ChildDatabaseBlock
     */
    public function setChildDatabase(ChildDatabaseProperty $childDatabase): self
    {
        $this->childDatabase = $childDatabase;

        return $this;
    }
}

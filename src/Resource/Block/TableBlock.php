<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Block;

use Brd6\NotionSdkPhp\Resource\AbstractBlock;
use Brd6\NotionSdkPhp\Resource\Property\TableProperty;

class TableBlock extends AbstractBlock
{
    protected ?TableProperty $table = null;

    protected function initializeBlockProperty(): void
    {
        $data = (array) $this->getRawData()[$this->getType()];
        $this->table = TableProperty::fromRawData($data);
    }

    public function getTable(): ?TableProperty
    {
        return $this->table;
    }

    public function setTable(?TableProperty $table): self
    {
        $this->table = $table;

        return $this;
    }
}

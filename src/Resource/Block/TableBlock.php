<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Block;

use Brd6\NotionSdkPhp\Exception\InvalidResourceException;
use Brd6\NotionSdkPhp\Exception\InvalidResourceTypeException;
use Brd6\NotionSdkPhp\Exception\UnsupportedUserTypeException;
use Brd6\NotionSdkPhp\Resource\Property\TableProperty;

class TableBlock extends AbstractBlock
{
    protected ?TableProperty $table = null;

    /**
     * @throws InvalidResourceException
     * @throws InvalidResourceTypeException
     * @throws UnsupportedUserTypeException
     */
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

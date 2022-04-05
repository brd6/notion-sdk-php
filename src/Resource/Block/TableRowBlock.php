<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Block;

use Brd6\NotionSdkPhp\Exception\InvalidResourceException;
use Brd6\NotionSdkPhp\Exception\InvalidResourceTypeException;
use Brd6\NotionSdkPhp\Exception\UnsupportedUserTypeException;
use Brd6\NotionSdkPhp\Resource\Property\TableRowProperty;

class TableRowBlock extends AbstractBlock
{
    protected ?TableRowProperty $tableRow = null;

    /**
     * @throws InvalidResourceException
     * @throws InvalidResourceTypeException
     * @throws UnsupportedUserTypeException
     */
    protected function initializeBlockProperty(): void
    {
        $data = (array) $this->getRawData()[$this->getType()];
        $this->tableRow = TableRowProperty::fromRawData($data);
    }

    public function getTableRow(): ?TableRowProperty
    {
        return $this->tableRow;
    }

    public function setTableRow(?TableRowProperty $tableRow): self
    {
        $this->tableRow = $tableRow;

        return $this;
    }
}

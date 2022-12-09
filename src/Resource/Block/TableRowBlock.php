<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Block;

use Brd6\NotionSdkPhp\Exception\InvalidRichTextException;
use Brd6\NotionSdkPhp\Exception\UnsupportedRichTextTypeException;
use Brd6\NotionSdkPhp\Resource\Property\TableRowProperty;

class TableRowBlock extends AbstractBlock
{
    protected ?TableRowProperty $tableRow = null;

    /**
     * @throws InvalidRichTextException
     * @throws UnsupportedRichTextTypeException
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

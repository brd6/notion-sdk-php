<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Block;

use Brd6\NotionSdkPhp\Exception\InvalidRichTextException;
use Brd6\NotionSdkPhp\Exception\UnsupportedRichTextTypeException;
use Brd6\NotionSdkPhp\Resource\Property\TableOfContentsProperty;

class TableOfContentsBlock extends AbstractBlock
{
    protected ?TableOfContentsProperty $tableOfContents = null;

    /**
     * @throws InvalidRichTextException
     * @throws UnsupportedRichTextTypeException
     */
    protected function initializeBlockProperty(): void
    {
        $data = (array) $this->getRawData()[$this->getType()];
        $this->tableOfContents = TableOfContentsProperty::fromRawData($data);
    }

    public function getTableOfContents(): ?TableOfContentsProperty
    {
        return $this->tableOfContents;
    }

    public function setTableOfContents(?TableOfContentsProperty $tableOfContents): self
    {
        $this->tableOfContents = $tableOfContents;

        return $this;
    }
}

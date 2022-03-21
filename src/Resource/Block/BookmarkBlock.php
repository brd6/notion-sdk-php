<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Block;

use Brd6\NotionSdkPhp\Exception\InvalidRichTextException;
use Brd6\NotionSdkPhp\Exception\UnsupportedRichTextTypeException;
use Brd6\NotionSdkPhp\Resource\AbstractBlock;
use Brd6\NotionSdkPhp\Resource\Property\BookmarkProperty;

class BookmarkBlock extends AbstractBlock
{
    protected ?BookmarkProperty $bookmark = null;

    /**
     * @throws InvalidRichTextException
     * @throws UnsupportedRichTextTypeException
     */
    protected function initializeBlockProperty(): void
    {
        $data = (array) $this->getRawData()[$this->getType()];
        $this->bookmark = BookmarkProperty::fromRawData($data);
    }

    public function getBookmark(): ?BookmarkProperty
    {
        return $this->bookmark;
    }

    public function setBookmark(?BookmarkProperty $bookmark): self
    {
        $this->bookmark = $bookmark;

        return $this;
    }
}

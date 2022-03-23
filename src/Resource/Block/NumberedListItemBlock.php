<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Block;

use Brd6\NotionSdkPhp\Exception\InvalidResourceException;
use Brd6\NotionSdkPhp\Exception\InvalidResourceTypeException;
use Brd6\NotionSdkPhp\Exception\InvalidRichTextException;
use Brd6\NotionSdkPhp\Exception\UnsupportedRichTextTypeException;
use Brd6\NotionSdkPhp\Resource\Property\NumberedListItemProperty;

class NumberedListItemBlock extends AbstractBlock
{
    protected ?NumberedListItemProperty $numberedListItem = null;

    /**
     * @throws InvalidResourceException
     * @throws InvalidResourceTypeException
     * @throws InvalidRichTextException
     * @throws UnsupportedRichTextTypeException
     */
    protected function initializeBlockProperty(): void
    {
        $data = (array) $this->getRawData()[$this->getType()];

        /** @var NumberedListItemProperty $property */
        $property = NumberedListItemProperty::fromRawData($data);

        $this->numberedListItem = $property;
    }

    public function getNumberedListItem(): ?NumberedListItemProperty
    {
        return $this->numberedListItem;
    }

    public function setNumberedListItem(?NumberedListItemProperty $numberedListItem): self
    {
        $this->numberedListItem = $numberedListItem;

        return $this;
    }
}

<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Block;

use Brd6\NotionSdkPhp\Exception\InvalidResourceException;
use Brd6\NotionSdkPhp\Exception\InvalidResourceTypeException;
use Brd6\NotionSdkPhp\Exception\InvalidRichTextException;
use Brd6\NotionSdkPhp\Exception\UnsupportedRichTextTypeException;
use Brd6\NotionSdkPhp\Exception\UnsupportedUserTypeException;
use Brd6\NotionSdkPhp\Resource\Property\ParagraphProperty;

class ParagraphBlock extends AbstractBlock
{
    protected ?ParagraphProperty $paragraph = null;

    /**
     * @throws InvalidResourceException
     * @throws InvalidResourceTypeException
     * @throws InvalidRichTextException
     * @throws UnsupportedRichTextTypeException
     * @throws UnsupportedUserTypeException
     */
    protected function initializeBlockProperty(): void
    {
        $data = (array) $this->getRawData()[$this->getType()];

        /** @var ParagraphProperty $property */
        $property = ParagraphProperty::fromRawData($data);

        $this->paragraph = $property;
    }

    public function getParagraph(): ?ParagraphProperty
    {
        return $this->paragraph;
    }

    /**
     * @param ParagraphProperty|null $paragraph
     *
     * @return ParagraphBlock
     */
    public function setParagraph(?ParagraphProperty $paragraph): self
    {
        $this->paragraph = $paragraph;

        return $this;
    }
}

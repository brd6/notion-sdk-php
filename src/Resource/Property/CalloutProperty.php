<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Property;

use Brd6\NotionSdkPhp\Exception\InvalidFileException;
use Brd6\NotionSdkPhp\Exception\InvalidResourceException;
use Brd6\NotionSdkPhp\Exception\InvalidResourceTypeException;
use Brd6\NotionSdkPhp\Exception\InvalidRichTextException;
use Brd6\NotionSdkPhp\Exception\UnsupportedFileTypeException;
use Brd6\NotionSdkPhp\Exception\UnsupportedRichTextTypeException;
use Brd6\NotionSdkPhp\Resource\File\AbstractFile;

class CalloutProperty extends AbstractParagraphProperty
{
    protected ?AbstractFile $icon = null;

    /**
     * @param array $rawData
     *
     * @return CalloutProperty
     *
     * @throws InvalidResourceException
     * @throws InvalidResourceTypeException
     * @throws InvalidRichTextException
     * @throws UnsupportedRichTextTypeException
     * @throws InvalidFileException
     * @throws UnsupportedFileTypeException
     */
    public static function fromRawData(array $rawData): self
    {
        /** @var self $property */
        $property = parent::fromRawData($rawData);

        $property->icon = AbstractFile::fromRawData((array) $rawData['icon']);

        return $property;
    }

    public function getIcon(): ?AbstractFile
    {
        return $this->icon;
    }

    public function setIcon(?AbstractFile $icon): self
    {
        $this->icon = $icon;

        return $this;
    }
}

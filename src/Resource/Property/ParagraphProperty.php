<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Property;

use Brd6\NotionSdkPhp\Resource\File\AbstractFile;

class ParagraphProperty extends AbstractParagraphProperty
{
    protected ?AbstractFile $icon = null;

    public static function fromRawData(array $rawData): self
    {
        /** @var self $property */
        $property = parent::fromRawData($rawData);
        $property->icon = isset($rawData['icon']) ? AbstractFile::fromRawData((array) $rawData['icon']) : null;

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

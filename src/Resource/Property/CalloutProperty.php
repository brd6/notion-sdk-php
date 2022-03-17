<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Property;

use Brd6\NotionSdkPhp\Exception\InvalidFileException;
use Brd6\NotionSdkPhp\Exception\InvalidResourceException;
use Brd6\NotionSdkPhp\Exception\InvalidResourceTypeException;
use Brd6\NotionSdkPhp\Exception\InvalidRichTextException;
use Brd6\NotionSdkPhp\Exception\UnsupportedFileTypeException;
use Brd6\NotionSdkPhp\Exception\UnsupportedRichTextTypeException;
use Brd6\NotionSdkPhp\Resource\AbstractBlock;
use Brd6\NotionSdkPhp\Resource\AbstractFile;
use Brd6\NotionSdkPhp\Resource\AbstractProperty;
use Brd6\NotionSdkPhp\Resource\AbstractRichText;

use function array_map;

class CalloutProperty extends AbstractProperty
{
    /**
     * @var array|AbstractRichText[]
     */
    protected array $richTexts = [];

    protected ?AbstractFile $icon = null;

    protected string $color = '';

    /**
     * @var array|AbstractBlock[]
     */
    protected array $children = [];

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
        $property = new self();
        $property->color = (string) $rawData['color'];
        $property->richTexts = array_map(
            fn (array $richTextRawData) => AbstractRichText::fromRawData($richTextRawData),
            (array) $rawData['rich_text'],
        );

        $property->children = isset($rawData['children']) ? array_map(
            fn (array $childRawData) => AbstractBlock::fromRawData($childRawData),
            (array) $rawData['children'],
        ) : [];

        $property->icon = AbstractFile::fromRawData((array) $rawData['icon']);

        return $property;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    /**
     * @param string $color
     *
     * @return CalloutProperty
     */
    public function setColor(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    /**
     * @return array|AbstractRichText[]
     */
    public function getRichTexts(): array
    {
        return $this->richTexts;
    }

    /**
     * @param array|AbstractRichText[] $richTexts
     *
     * @return CalloutProperty
     */
    public function setRichTexts(array $richTexts): self
    {
        $this->richTexts = $richTexts;

        return $this;
    }

    /**
     * @return array|AbstractBlock[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * @param array|AbstractBlock[] $children
     *
     * @return CalloutProperty
     */
    public function setChildren(array $children): self
    {
        $this->children = $children;

        return $this;
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

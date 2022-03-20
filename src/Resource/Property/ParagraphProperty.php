<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Property;

use Brd6\NotionSdkPhp\Exception\InvalidResourceException;
use Brd6\NotionSdkPhp\Exception\InvalidResourceTypeException;
use Brd6\NotionSdkPhp\Exception\InvalidRichTextException;
use Brd6\NotionSdkPhp\Exception\UnsupportedRichTextTypeException;
use Brd6\NotionSdkPhp\Resource\AbstractBlock;
use Brd6\NotionSdkPhp\Resource\AbstractProperty;
use Brd6\NotionSdkPhp\Resource\AbstractRichText;

use function array_map;

class ParagraphProperty extends AbstractProperty
{
    protected string $color = '';

    /**
     * @var array|AbstractRichText[]
     */
    protected array $richText = [];

    /**
     * @var array|AbstractBlock[]
     */
    protected array $children = [];

    /**
     * @param array $rawData
     *
     * @return ParagraphProperty
     *
     * @throws InvalidRichTextException
     * @throws UnsupportedRichTextTypeException
     * @throws InvalidResourceException
     * @throws InvalidResourceTypeException
     */
    public static function fromRawData(array $rawData): self
    {
        $property = new self();
        $property->color = (string) $rawData['color'];
        $property->richText = array_map(
            fn (array $richTextRawData) => AbstractRichText::fromRawData($richTextRawData),
            (array) $rawData['rich_text'],
        );

        $property->children = isset($rawData['children']) ? array_map(
            fn (array $childRawData) => AbstractBlock::fromRawData($childRawData),
            (array) $rawData['children'],
        ) : [];

        return $property;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    /**
     * @param string $color
     *
     * @return ParagraphProperty
     */
    public function setColor(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    /**
     * @return array|AbstractRichText[]
     */
    public function getRichText(): array
    {
        return $this->richText;
    }

    /**
     * @param array|AbstractRichText[] $richText
     *
     * @return ParagraphProperty
     */
    public function setRichText(array $richText): self
    {
        $this->richText = $richText;

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
     * @return ParagraphProperty
     */
    public function setChildren(array $children): self
    {
        $this->children = $children;

        return $this;
    }
}

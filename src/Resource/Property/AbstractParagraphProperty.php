<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Property;

use Brd6\NotionSdkPhp\Exception\InvalidResourceException;
use Brd6\NotionSdkPhp\Exception\InvalidResourceTypeException;
use Brd6\NotionSdkPhp\Exception\InvalidRichTextException;
use Brd6\NotionSdkPhp\Exception\UnsupportedRichTextTypeException;
use Brd6\NotionSdkPhp\Exception\UnsupportedUserTypeException;
use Brd6\NotionSdkPhp\Resource\Block\AbstractBlock;
use Brd6\NotionSdkPhp\Resource\RichText\AbstractRichText;

use function array_map;

abstract class AbstractParagraphProperty extends AbstractProperty
{
    protected ?string $color = null;

    /**
     * @var array|AbstractRichText[]
     */
    protected array $richText = [];

    /**
     * @var array|AbstractBlock[]
     */
    protected array $children = [];

    final public function __construct()
    {
    }

    /**
     * @param array $rawData
     *
     * @return AbstractParagraphProperty
     *
     * @throws InvalidResourceException
     * @throws InvalidResourceTypeException
     * @throws InvalidRichTextException
     * @throws UnsupportedRichTextTypeException
     * @throws UnsupportedUserTypeException
     */
    public static function fromRawData(array $rawData): self
    {
        $property = new static();
        $property->color = isset($rawData['color']) ? (string) $rawData['color'] : null;
        $property->richText = isset($rawData['rich_text']) ? array_map(
            fn (array $richTextRawData) => AbstractRichText::fromRawData($richTextRawData),
            (array) $rawData['rich_text'],
        ) : [];

        $property->children = isset($rawData['children']) ? array_map(
            fn (array $childRawData) => AbstractBlock::fromRawData($childRawData),
            (array) $rawData['children'],
        ) : [];

        return $property;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

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
     */
    public function setChildren(array $children): self
    {
        $this->children = $children;

        return $this;
    }
}

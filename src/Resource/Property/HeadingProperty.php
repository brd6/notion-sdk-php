<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Property;

use Brd6\NotionSdkPhp\Exception\InvalidRichTextException;
use Brd6\NotionSdkPhp\Exception\UnsupportedRichTextTypeException;
use Brd6\NotionSdkPhp\Resource\AbstractProperty;
use Brd6\NotionSdkPhp\Resource\AbstractRichText;

use function array_map;

class HeadingProperty extends AbstractProperty
{
    protected string $color = '';

    /**
     * @var array|AbstractRichText[]
     */
    protected array $richText = [];

    /**
     * @throws InvalidRichTextException
     * @throws UnsupportedRichTextTypeException
     */
    public static function fromRawData(array $rawData): self
    {
        $property = new self();
        $property->color = (string) $rawData['color'];

        $property->richText = array_map(
            fn (array $richTextRawData) => AbstractRichText::fromRawData($richTextRawData),
            (array) $rawData['rich_text'],
        );

        return $property;
    }

    public function getColor(): string
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

    public function setRichText(array $richText): self
    {
        $this->richText = $richText;

        return $this;
    }
}

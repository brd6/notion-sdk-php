<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Property;

use Brd6\NotionSdkPhp\Exception\InvalidRichTextException;
use Brd6\NotionSdkPhp\Exception\UnsupportedRichTextTypeException;
use Brd6\NotionSdkPhp\Resource\RichText\AbstractRichText;

use function array_map;

class BookmarkProperty extends AbstractProperty
{
    /**
     * @var array|AbstractRichText[]
     */
    protected array $caption = [];

    protected string $url = '';

    /**
     * @throws InvalidRichTextException
     * @throws UnsupportedRichTextTypeException
     */
    public static function fromRawData(array $rawData): self
    {
        $property = new self();

        $property->caption = isset($rawData['caption']) ? array_map(
            fn (array $richTextRawData) => AbstractRichText::fromRawData($richTextRawData),
            (array) $rawData['caption'],
        ) : [];
        $property->url = (string) $rawData['url'];

        return $property;
    }

    /**
     * @return array|AbstractRichText[]
     */
    public function getCaption(): array
    {
        return $this->caption;
    }

    /**
     * @param array|AbstractRichText[] $caption
     */
    public function setCaption(array $caption): self
    {
        $this->caption = $caption;

        return $this;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }
}

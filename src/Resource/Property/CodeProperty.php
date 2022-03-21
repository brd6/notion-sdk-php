<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Property;

use Brd6\NotionSdkPhp\Exception\InvalidRichTextException;
use Brd6\NotionSdkPhp\Exception\UnsupportedRichTextTypeException;
use Brd6\NotionSdkPhp\Resource\AbstractParagraphProperty;
use Brd6\NotionSdkPhp\Resource\AbstractRichText;

use function array_map;

class CodeProperty extends AbstractParagraphProperty
{
    /**
     * @var array|AbstractRichText[]
     */
    protected array $richText = [];

    /**
     * @var array|AbstractRichText[]
     */
    protected array $caption = [];

    protected string $language = '';

    /**
     * @throws InvalidRichTextException
     * @throws UnsupportedRichTextTypeException
     */
    public static function fromRawData(array $rawData): self
    {
        $property = new self();
        $property->richText = isset($rawData['rich_text']) ? array_map(
            fn (array $richTextRawData) => AbstractRichText::fromRawData($richTextRawData),
            (array) $rawData['rich_text'],
        ) : [];
        $property->caption = isset($rawData['caption']) ? array_map(
            fn (array $richTextRawData) => AbstractRichText::fromRawData($richTextRawData),
            (array) $rawData['caption'],
        ) : [];
        $property->language = (string) $rawData['language'];

        return $property;
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

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function setLanguage(string $language): self
    {
        $this->language = $language;

        return $this;
    }
}

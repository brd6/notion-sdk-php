<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Page\PropertyValue;

use Brd6\NotionSdkPhp\Resource\RichText\AbstractRichText;

use function array_map;

class RichTextPropertyValue extends AbstractPropertyValue
{
    /**
     * @var array|AbstractRichText[]
     */
    protected array $richText = [];

    protected function initialize(): void
    {
        $data = (array) $this->getRawData()[$this->getType()];

        $this->richText = array_map(
            fn (array $richTextRawData) => AbstractRichText::fromRawData($richTextRawData),
            $data,
        );
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
}

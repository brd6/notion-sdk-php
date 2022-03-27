<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Page\PropertyValue;

use Brd6\NotionSdkPhp\Resource\Page\AbstractPropertyValue;
use Brd6\NotionSdkPhp\Resource\RichText\AbstractRichText;

use function array_map;

class TitlePropertyValue extends AbstractPropertyValue
{
    /**
     * @var array|AbstractRichText[]
     */
    protected array $title = [];

    protected function initialize(): void
    {
        $data = (array) $this->getRawData()[$this->getType()];

        $this->title = array_map(
            fn (array $richTextRawData) => AbstractRichText::fromRawData($richTextRawData),
            $data,
        );
    }

    /**
     * @return array|AbstractRichText[]
     */
    public function getTitle(): array
    {
        return $this->title;
    }

    /**
     * @param array|AbstractRichText[] $title
     */
    public function setTitle(array $title): self
    {
        $this->title = $title;

        return $this;
    }
}
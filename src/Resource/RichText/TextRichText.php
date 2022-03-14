<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\RichText;

use Brd6\NotionSdkPhp\Resource\AbstractRichText;
use Brd6\NotionSdkPhp\Resource\Link;

class TextRichText extends AbstractRichText
{
    public const RICH_TEXT_TYPE = 'text';

    protected string $content = '';
    protected ?Link $link = null;

    public static function getRichTextType(): string
    {
        return self::RICH_TEXT_TYPE;
    }

    protected function initialize(): void
    {
        $data = (array) $this->getRawData()[$this->getType()];
        $this->content = (string) $data['content'];
        $this->link = $data['link'] !== null ? Link::fromRawData((array) $data['link']) : null;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getLink(): ?Link
    {
        return $this->link;
    }

    public function setLink(?Link $link): self
    {
        $this->link = $link;

        return $this;
    }
}

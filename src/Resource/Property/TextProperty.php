<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Property;

use Brd6\NotionSdkPhp\Resource\AbstractProperty;
use Brd6\NotionSdkPhp\Resource\Link;

class TextProperty extends AbstractProperty
{
    protected string $content = '';
    protected ?Link $link = null;

    public static function fromRawData(array $rawData): self
    {
        $property = new self();

        $property->content = (string) $rawData['content'];
        $property->link = $rawData['link'] !== null ? Link::fromRawData((array) $rawData['link']) : null;

        return $property;
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

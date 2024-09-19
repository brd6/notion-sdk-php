<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Property;

class LinkMentionProperty extends AbstractProperty
{
    protected string $href = '';
    protected string $title = '';

    public static function fromRawData(array $rawData): self
    {
        $property = new self();

        $property->href = (string) $rawData['href'];
        $property->title = (string) $rawData['title'];

        return $property;
    }

    public function getHref(): string
    {
        return $this->href;
    }

    public function setHref(string $href): self
    {
        $this->href = $href;

        return $this;
    }
}

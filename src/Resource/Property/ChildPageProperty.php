<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Property;

use Brd6\NotionSdkPhp\Resource\AbstractProperty;

class ChildPageProperty extends AbstractProperty
{
    private string $title = '';

    public static function fromRawData(array $rawData): self
    {
        $property = new self();
        $property->title = (string) $rawData['title'];

        return $property;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return ChildPageProperty
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }
}

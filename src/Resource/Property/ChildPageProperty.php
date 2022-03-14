<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Property;

class ChildPageProperty
{
    private string $title = '';

    public static function fromData(array $data): self
    {
        $property = new self();
        $property->title = (string) $data['title'];

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

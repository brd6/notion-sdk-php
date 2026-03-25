<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Property;

use function rawurlencode;

class IconProperty extends AbstractProperty
{
    private const NOTION_ICONS_BASE_URL = 'https://www.notion.so/icons';

    protected string $name = '';
    protected string $color = '';

    public static function fromRawData(array $rawData): self
    {
        $property = new self();

        $property->name = (string) ($rawData['name'] ?? '');
        $property->color = (string) ($rawData['color'] ?? '');

        return $property;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
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

    public function getUrl(string $mode = 'light'): string
    {
        if ($this->name === '' || $this->color === '') {
            return '';
        }

        $mode = $mode === 'dark' ? 'dark' : 'light';

        return self::NOTION_ICONS_BASE_URL
            . '/'
            . rawurlencode($this->name)
            . '_'
            . rawurlencode($this->color)
            . '.svg?mode='
            . $mode;
    }
}

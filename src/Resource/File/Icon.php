<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\File;

use Brd6\NotionSdkPhp\Resource\Property\IconProperty;

class Icon extends AbstractFile
{
    public const FILE_TYPE = 'icon';

    protected ?IconProperty $icon = null;

    public static function getFileType(): string
    {
        return self::FILE_TYPE;
    }

    protected function initialize(): void
    {
        $data = (array) $this->getRawData()[$this->getType()];
        $this->icon = IconProperty::fromRawData($data);
    }

    public function getIcon(): ?IconProperty
    {
        return $this->icon;
    }

    public function setIcon(IconProperty $icon): self
    {
        $this->icon = $icon;

        return $this;
    }
}

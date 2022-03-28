<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\File;

use Brd6\NotionSdkPhp\Resource\Property\ExternalProperty;

class External extends AbstractFile
{
    public const FILE_TYPE = 'external';

    protected ?ExternalProperty $external = null;

    public static function getFileType(): string
    {
        return self::FILE_TYPE;
    }

    protected function initialize(): void
    {
        $data = (array) $this->getRawData()[$this->getType()];
        $this->external = ExternalProperty::fromRawData($data);
    }

    public function getExternal(): ?ExternalProperty
    {
        return $this->external;
    }

    public function setExternal(ExternalProperty $external): self
    {
        $this->external = $external;

        return $this;
    }
}

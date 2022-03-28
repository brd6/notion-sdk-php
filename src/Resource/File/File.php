<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\File;

use Brd6\NotionSdkPhp\Resource\Property\FileProperty;

class File extends AbstractFile
{
    public const FILE_TYPE = 'file';

    protected ?FileProperty $file = null;

    public static function getFileType(): string
    {
        return self::FILE_TYPE;
    }

    protected function initialize(): void
    {
        $data = (array) $this->getRawData()[$this->getType()];
        $this->file = FileProperty::fromRawData($data);
    }

    public function getFile(): ?FileProperty
    {
        return $this->file;
    }

    public function setFile(FileProperty $file): self
    {
        $this->file = $file;

        return $this;
    }
}

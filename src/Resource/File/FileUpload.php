<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\File;

use Brd6\NotionSdkPhp\Resource\Property\FileUploadProperty;

class FileUpload extends AbstractFile
{
    public const FILE_TYPE = 'file_upload';

    protected ?FileUploadProperty $fileUpload = null;

    public static function getFileType(): string
    {
        return self::FILE_TYPE;
    }

    protected function initialize(): void
    {
        $data = (array) $this->getRawData()[$this->getType()];
        $this->fileUpload = FileUploadProperty::fromRawData($data);
    }

    public function getFileUpload(): ?FileUploadProperty
    {
        return $this->fileUpload;
    }

    public function setFileUpload(FileUploadProperty $fileUpload): self
    {
        $this->fileUpload = $fileUpload;

        return $this;
    }
}

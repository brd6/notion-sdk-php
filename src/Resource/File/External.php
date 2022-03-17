<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\File;

use Brd6\NotionSdkPhp\Resource\AbstractFile;

class External extends AbstractFile
{
    public const FILE_TYPE = 'external';

    protected string $url = '';

    public static function getFileType(): string
    {
        return self::FILE_TYPE;
    }

    protected function initialize(): void
    {
        $data = (array) $this->getRawData()[$this->getType()];
        $this->url = (string) $data['url'];
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }
}

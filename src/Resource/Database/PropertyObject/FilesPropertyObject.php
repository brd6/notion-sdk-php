<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Database\PropertyObject;

use Brd6\NotionSdkPhp\Resource\Database\PropertyConfiguration\FilesPropertyConfiguration;

class FilesPropertyObject extends AbstractPropertyObject
{
    protected ?FilesPropertyConfiguration $files = null;

    public function __construct()
    {
        $this->files = new FilesPropertyConfiguration();
    }

    protected function initialize(): void
    {
        $this->files = isset($this->getRawData()['files']) ?
            FilesPropertyConfiguration::fromRawData((array) $this->getRawData()['files']) :
            null;
    }

    public function getFiles(): ?FilesPropertyConfiguration
    {
        return $this->files;
    }

    public function setFiles(?FilesPropertyConfiguration $files): self
    {
        $this->files = $files;

        return $this;
    }
}

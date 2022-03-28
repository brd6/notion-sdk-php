<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Page\PropertyValue;

class FilePropertyValue extends AbstractFilePropertyValue
{
    protected string $file = '';

    protected function initialize(): void
    {
        $this->file = (string) $this->getRawData()[$this->getType()];
    }

    public function getFile(): string
    {
        return $this->file;
    }

    public function setFile(string $file): self
    {
        $this->file = $file;

        return $this;
    }
}

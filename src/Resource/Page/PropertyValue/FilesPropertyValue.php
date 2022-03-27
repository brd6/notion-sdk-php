<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Page\PropertyValue;

use Brd6\NotionSdkPhp\Resource\File\AbstractFile;
use Brd6\NotionSdkPhp\Resource\Page\AbstractPropertyValue;

use function array_map;

class FilesPropertyValue extends AbstractPropertyValue
{
    /**
     * @var array|AbstractFile[]
     */
    protected array $files = [];

    protected function initialize(): void
    {
        $data = (array) $this->getRawData()[$this->getType()];

        $this->files = array_map(
            fn (array $filesRawData) => AbstractFile::fromRawData($filesRawData),
            $data,
        );
    }

    /**
     * @return array|AbstractFile[]
     */
    public function getFiles(): array
    {
        return $this->files;
    }

    /**
     * @param array|AbstractFile[] $files
     */
    public function setFiles(array $files): self
    {
        $this->files = $files;

        return $this;
    }
}

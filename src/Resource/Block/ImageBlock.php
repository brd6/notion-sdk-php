<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Block;

use Brd6\NotionSdkPhp\Exception\InvalidFileException;
use Brd6\NotionSdkPhp\Exception\UnsupportedFileTypeException;
use Brd6\NotionSdkPhp\Resource\AbstractBlock;
use Brd6\NotionSdkPhp\Resource\AbstractFile;

class ImageBlock extends AbstractBlock
{
    protected ?AbstractFile $image = null;

    /**
     * @throws UnsupportedFileTypeException
     * @throws InvalidFileException
     */
    protected function initializeBlockProperty(): void
    {
        $data = (array) $this->getRawData()[$this->getType()];
        $this->image = AbstractFile::fromRawData($data);
    }

    public function getImage(): ?AbstractFile
    {
        return $this->image;
    }

    public function setImage(?AbstractFile $image): self
    {
        $this->image = $image;

        return $this;
    }
}

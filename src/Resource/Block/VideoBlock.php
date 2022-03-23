<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Block;

use Brd6\NotionSdkPhp\Exception\InvalidFileException;
use Brd6\NotionSdkPhp\Exception\UnsupportedFileTypeException;
use Brd6\NotionSdkPhp\Resource\File\AbstractFile;

class VideoBlock extends AbstractBlock
{
    protected ?AbstractFile $video = null;

    /**
     * @throws UnsupportedFileTypeException
     * @throws InvalidFileException
     */
    protected function initializeBlockProperty(): void
    {
        $data = (array) $this->getRawData()[$this->getType()];
        $this->video = AbstractFile::fromRawData($data);
    }

    public function getVideo(): ?AbstractFile
    {
        return $this->video;
    }

    public function setVideo(?AbstractFile $video): self
    {
        $this->video = $video;

        return $this;
    }
}

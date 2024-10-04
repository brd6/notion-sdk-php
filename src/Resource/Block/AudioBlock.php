<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Block;

use Brd6\NotionSdkPhp\Exception\InvalidFileException;
use Brd6\NotionSdkPhp\Exception\UnsupportedFileTypeException;
use Brd6\NotionSdkPhp\Resource\File\AbstractFile;

class AudioBlock extends AbstractBlock
{
    protected ?AbstractFile $audio = null;

    /**
     * @throws InvalidFileException
     * @throws UnsupportedFileTypeException
     */
    protected function initializeBlockProperty(): void
    {
        $data = (array) $this->getRawData()[$this->getType()];
        $this->audio = AbstractFile::fromRawData($data);
    }

    public function getAudio(): ?AbstractFile
    {
        return $this->audio;
    }

    public function setAudio(?AbstractFile $audio): self
    {
        $this->audio = $audio;

        return $this;
    }
}

<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Block;

use Brd6\NotionSdkPhp\Exception\InvalidFileException;
use Brd6\NotionSdkPhp\Exception\UnsupportedFileTypeException;
use Brd6\NotionSdkPhp\Resource\File\AbstractFile;

class PdfBlock extends AbstractBlock
{
    protected ?AbstractFile $pdf = null;

    /**
     * @throws UnsupportedFileTypeException
     * @throws InvalidFileException
     */
    protected function initializeBlockProperty(): void
    {
        $data = (array) $this->getRawData()[$this->getType()];
        $this->pdf = AbstractFile::fromRawData($data);
    }

    public function getPdf(): ?AbstractFile
    {
        return $this->pdf;
    }

    public function setPdf(?AbstractFile $pdf): self
    {
        $this->pdf = $pdf;

        return $this;
    }
}

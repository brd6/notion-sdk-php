<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Block;

use Brd6\NotionSdkPhp\Exception\InvalidFileException;
use Brd6\NotionSdkPhp\Exception\InvalidRichTextException;
use Brd6\NotionSdkPhp\Exception\UnsupportedFileTypeException;
use Brd6\NotionSdkPhp\Exception\UnsupportedRichTextTypeException;
use Brd6\NotionSdkPhp\Resource\AbstractBlock;
use Brd6\NotionSdkPhp\Resource\AbstractFile;
use Brd6\NotionSdkPhp\Resource\AbstractRichText;

use function array_map;

class FileBlock extends AbstractBlock
{
    protected ?AbstractFile $file = null;
    /**
     * @var array|AbstractRichText[]
     */
    protected array $caption = [];

    /**
     * @throws InvalidFileException
     * @throws UnsupportedFileTypeException
     * @throws InvalidRichTextException
     * @throws UnsupportedRichTextTypeException
     */
    protected function initializeBlockProperty(): void
    {
        $data = (array) $this->getRawData()[$this->getType()];
        $this->file = AbstractFile::fromRawData($data);
        $this->caption = isset($rawData['caption']) ? array_map(
            fn (array $richTextRawData) => AbstractRichText::fromRawData($richTextRawData),
            (array) $rawData['caption'],
        ) : [];
    }

    public function getFile(): ?AbstractFile
    {
        return $this->file;
    }

    public function setFile(?AbstractFile $file): self
    {
        $this->file = $file;

        return $this;
    }

    /**
     * @return array|AbstractRichText[]
     */
    public function getCaption(): array
    {
        return $this->caption;
    }

    /**
     * @param array|AbstractRichText[] $caption
     */
    public function setCaption(array $caption): self
    {
        $this->caption = $caption;

        return $this;
    }
}

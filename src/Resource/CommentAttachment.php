<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource;

use Brd6\NotionSdkPhp\Exception\InvalidFileException;
use Brd6\NotionSdkPhp\Exception\UnsupportedFileTypeException;
use Brd6\NotionSdkPhp\Resource\File\AbstractFile;

use function strlen;

class CommentAttachment extends AbstractJsonSerializable
{
    protected string $category = '';
    protected ?AbstractFile $file = null;
    private array $rawData = [];

    /**
     * @throws InvalidFileException
     * @throws UnsupportedFileTypeException
     */
    public static function fromRawData(array $rawData): self
    {
        $attachment = new self();
        $attachment->setRawData($rawData);
        $attachment->initialize();

        return $attachment;
    }

    protected function setRawData(array $rawData): self
    {
        $this->rawData = $rawData;

        return $this;
    }

    /**
     * @throws InvalidFileException
     * @throws UnsupportedFileTypeException
     */
    protected function initialize(): void
    {
        $this->category = (string) ($this->getRawData()['category'] ?? '');

        $this->file = isset($this->getRawData()['file']) ?
            AbstractFile::fromRawData((array) $this->getRawData()['file']) :
            null;
    }

    public function getRawData(): array
    {
        return $this->rawData;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function setCategory(string $category): self
    {
        $this->category = $category;

        return $this;
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

    public function toArray(bool $ignoreEmptyValue = true, array $onlyKeys = []): array
    {
        $data = [];

        if (strlen($this->category) > 0) {
            $data['category'] = $this->category;
        }

        if ($this->file !== null) {
            $data['file'] = $this->file->toArray();
        }

        return $data;
    }
}

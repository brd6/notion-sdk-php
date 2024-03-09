<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\File;

use Brd6\NotionSdkPhp\Exception\InvalidFileException;
use Brd6\NotionSdkPhp\Exception\UnsupportedFileTypeException;
use Brd6\NotionSdkPhp\Resource\AbstractJsonSerializable;
use Brd6\NotionSdkPhp\Resource\RichText\AbstractRichText;
use Brd6\NotionSdkPhp\Util\StringHelper;

use function array_map;
use function class_exists;

abstract class AbstractFile extends AbstractJsonSerializable
{
    private array $rawData = [];
    protected string $type = '';

    /**
     * @var array|AbstractRichText[]
     */
    protected array $caption = [];

    public function __construct()
    {
        $this->type = static::getFileType();
    }

    /**
     * @throws InvalidFileException
     * @throws UnsupportedFileTypeException
     */
    public static function fromRawData(array $rawData): self
    {
        if (!isset($rawData['type'])) {
            throw new InvalidFileException();
        }

        $class = static::getMapClassFromType((string) $rawData['type']);

        /** @var static $resource */
        $resource = new $class();

        $resource
            ->setRawData($rawData)
            ->initialize();

        return $resource;
    }

    protected function setRawData(array $rawData): self
    {
        $this->rawData = $rawData;

        $this->type = (string) ($this->rawData['type'] ?? '');
        $this->caption = isset($this->rawData['caption']) ? array_map(
            fn (array $richTextRawData) => AbstractRichText::fromRawData($richTextRawData),
            (array) $this->rawData['caption'],
        ) : [];

        return $this;
    }

    /**
     * @throws UnsupportedFileTypeException
     */
    protected static function getMapClassFromType(string $type): string
    {
        $typeFormatted = StringHelper::snakeCaseToCamelCase($type);
        $class = "Brd6\\NotionSdkPhp\\Resource\\File\\$typeFormatted";

        if (!class_exists($class)) {
            throw new UnsupportedFileTypeException($type);
        }

        return $class;
    }

    public static function getFileType(): string
    {
        return '';
    }

    abstract protected function initialize(): void;

    public function getRawData(): array
    {
        return $this->rawData;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

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

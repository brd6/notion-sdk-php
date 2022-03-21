<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\RichText;

use Brd6\NotionSdkPhp\Exception\InvalidMentionException;
use Brd6\NotionSdkPhp\Exception\UnsupportedMentionTypeException;
use Brd6\NotionSdkPhp\Resource\AbstractJsonSerializable;
use Brd6\NotionSdkPhp\Util\StringHelper;

use function class_exists;

abstract class AbstractMention extends AbstractJsonSerializable implements MentionInterface
{
    private array $rawData = [];
    protected string $type = '';

    /**
     * @throws InvalidMentionException
     * @throws UnsupportedMentionTypeException
     */
    public static function fromRawData(array $rawData): self
    {
        if (!isset($rawData['type'])) {
            throw new InvalidMentionException();
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

        return $this;
    }

    /**
     * @throws UnsupportedMentionTypeException
     */
    protected static function getMapClassFromType(string $type): string
    {
        $typeFormatted = StringHelper::snakeCaseToCamelCase($type);
        $class = "Brd6\\NotionSdkPhp\\Resource\\RichText\\Mention\\{$typeFormatted}Mention";

        if (!class_exists($class)) {
            throw new UnsupportedMentionTypeException($type);
        }

        return $class;
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
}

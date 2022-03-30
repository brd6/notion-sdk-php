<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Page\PropertyItem;

use Brd6\NotionSdkPhp\Exception\InvalidPropertyItemException;
use Brd6\NotionSdkPhp\Exception\UnsupportedPropertyItemException;
use Brd6\NotionSdkPhp\Resource\Property\AbstractProperty;
use Brd6\NotionSdkPhp\Util\StringHelper;

use function class_exists;

abstract class AbstractPropertyItem extends AbstractProperty
{
    private array $rawData = [];
    protected string $object = '';
    protected ?string $nextUrl = null;
    protected string $type = '';
    protected string $id = '';

    /**
     * @param array $rawData
     *
     * @return static
     *
     * @throws InvalidPropertyItemException
     * @throws UnsupportedPropertyItemException
     */
    public static function fromRawData(array $rawData): self
    {
        if (!isset($rawData['type'])) {
            throw new InvalidPropertyItemException();
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
        $this->id = (string) ($this->rawData['id'] ?? '');
        $this->object = (string) ($this->rawData['object'] ?? '');
        $this->nextUrl = (string) ($this->rawData['next_url'] ?? null);

        return $this;
    }

    /**
     * @throws UnsupportedPropertyItemException
     */
    protected static function getMapClassFromType(string $type): string
    {
        $typeFormatted = StringHelper::snakeCaseToCamelCase($type);
        $class = "Brd6\\NotionSdkPhp\\Resource\\Page\\PropertyItem\\{$typeFormatted}PropertyItem";

        if (!class_exists($class)) {
            throw new UnsupportedPropertyItemException($type);
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

    public function getObject(): string
    {
        return $this->object;
    }

    public function setObject(string $object): self
    {
        $this->object = $object;

        return $this;
    }

    public function getNextUrl(): ?string
    {
        return $this->nextUrl;
    }

    public function setNextUrl(string $nextUrl): self
    {
        $this->nextUrl = $nextUrl;

        return $this;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }
}

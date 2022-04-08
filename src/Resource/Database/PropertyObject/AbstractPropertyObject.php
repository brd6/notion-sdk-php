<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Database\PropertyObject;

use Brd6\NotionSdkPhp\Exception\InvalidPropertyObjectException;
use Brd6\NotionSdkPhp\Exception\UnsupportedPropertyObjectException;
use Brd6\NotionSdkPhp\Resource\Property\AbstractProperty;
use Brd6\NotionSdkPhp\Util\StringHelper;

use function class_exists;
use function in_array;

abstract class AbstractPropertyObject extends AbstractProperty
{
    private const SERIALIZER_KEYS_IGNORED = ['ignoreEmptyValue', 'rawData', 'type', 'id', 'name'];

    private array $rawData = [];
    protected string $type = '';
    protected string $id = '';
    protected string $name = '';

    /**
     * @param array $rawData
     *
     * @return static
     *
     * @throws InvalidPropertyObjectException
     * @throws UnsupportedPropertyObjectException
     */
    public static function fromRawData(array $rawData): self
    {
        if (!isset($rawData['type'])) {
            throw new InvalidPropertyObjectException();
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
        $this->name = (string) ($this->rawData['name'] ?? '');
        $this->id = (string) ($this->rawData['id'] ?? '');

        return $this;
    }

    /**
     * @throws UnsupportedPropertyObjectException
     */
    protected static function getMapClassFromType(string $type): string
    {
        $typeFormatted = StringHelper::snakeCaseToCamelCase($type);
        $class = "Brd6\\NotionSdkPhp\\Resource\\Database\\PropertyObject\\{$typeFormatted}PropertyObject";

        if (!class_exists($class)) {
            throw new UnsupportedPropertyObjectException($type);
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

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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

    /**
     * @param mixed $value
     */
    protected function canBeSerialized($value, string $key): bool
    {
        return !in_array($key, self::SERIALIZER_KEYS_IGNORED) ||
            parent::canBeSerialized($value, $key);
    }
}

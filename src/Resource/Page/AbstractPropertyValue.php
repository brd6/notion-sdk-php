<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Page;

use Brd6\NotionSdkPhp\Exception\InvalidPropertyValueException;
use Brd6\NotionSdkPhp\Exception\UnsupportedPropertyValueException;
use Brd6\NotionSdkPhp\Resource\Property\AbstractProperty;
use Brd6\NotionSdkPhp\Util\StringHelper;

use function class_exists;

abstract class AbstractPropertyValue extends AbstractProperty
{
    private array $rawData = [];
    protected string $type = '';
    protected string $id = '';

    /**
     * @param array $rawData
     *
     * @return static
     *
     * @throws InvalidPropertyValueException
     * @throws UnsupportedPropertyValueException
     */
    public static function fromRawData(array $rawData): self
    {
        if (!isset($rawData['type'])) {
            throw new InvalidPropertyValueException();
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

        return $this;
    }

    /**
     * @throws UnsupportedPropertyValueException
     */
    protected static function getMapClassFromType(string $type): string
    {
        $typeFormatted = StringHelper::snakeCaseToCamelCase($type);
        $class = "Brd6\\NotionSdkPhp\\Resource\\Page\\PropertyValue\\{$typeFormatted}PropertyValue";

        if (!class_exists($class)) {
            throw new UnsupportedPropertyValueException($type);
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

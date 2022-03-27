<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Property\Formula;

use Brd6\NotionSdkPhp\Exception\InvalidPropertyException;
use Brd6\NotionSdkPhp\Exception\UnsupportedPropertyTypeException;
use Brd6\NotionSdkPhp\Resource\Property\AbstractProperty;
use Brd6\NotionSdkPhp\Util\StringHelper;

use function class_exists;

abstract class AbstractFormulaProperty extends AbstractProperty
{
    public const PROPERTY_BASE_TYPE = 'formula';

    private array $rawData = [];
    protected string $type = '';

    /**
     * @param array $rawData
     *
     * @return static
     *
     * @throws InvalidPropertyException
     * @throws UnsupportedPropertyTypeException
     */
    public static function fromRawData(array $rawData): self
    {
        if (!isset($rawData['type'])) {
            throw new InvalidPropertyException(self::PROPERTY_BASE_TYPE);
        }

        $class = static::getMapClassFromType((string) $rawData['type']);

        /** @var static $property */
        $property = new $class();

        $property
            ->setRawData($rawData)
            ->initialize();

        return $property;
    }

    protected function setRawData(array $rawData): self
    {
        $this->rawData = $rawData;

        $this->type = (string) ($this->rawData['type'] ?? '');

        return $this;
    }

    /**
     * @throws UnsupportedPropertyTypeException
     */
    protected static function getMapClassFromType(string $type): string
    {
        $typeFormatted = StringHelper::snakeCaseToCamelCase($type);
        $class = "Brd6\\NotionSdkPhp\\Resource\\Property\\Formula\\{$typeFormatted}FormulaProperty";

        if (!class_exists($class)) {
            throw new UnsupportedPropertyTypeException($type, self::PROPERTY_BASE_TYPE);
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

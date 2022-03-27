<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Property\Formula;

use Brd6\NotionSdkPhp\Exception\InvalidParentException;
use Brd6\NotionSdkPhp\Exception\UnsupportedFormulaTypeException;
use Brd6\NotionSdkPhp\Resource\Property\AbstractProperty;
use Brd6\NotionSdkPhp\Util\StringHelper;

use function class_exists;

abstract class AbstractFormulaProperty extends AbstractProperty
{
    private array $rawData = [];
    protected string $type = '';

    /**
     * @param array $rawData
     *
     * @return static
     *
     * @throws InvalidParentException
     * @throws UnsupportedFormulaTypeException
     */
    public static function fromRawData(array $rawData): self
    {
        if (!isset($rawData['type'])) {
            throw new InvalidParentException();
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
     * @throws UnsupportedFormulaTypeException
     */
    protected static function getMapClassFromType(string $type): string
    {
        $typeFormatted = StringHelper::snakeCaseToCamelCase($type);
        $class = "Brd6\\NotionSdkPhp\\Resource\\Property\\Formula\\{$typeFormatted}FormulaProperty";

        if (!class_exists($class)) {
            throw new UnsupportedFormulaTypeException($type);
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

<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource;

use Brd6\NotionSdkPhp\Exception\InvalidResourceException;
use Brd6\NotionSdkPhp\Exception\InvalidResourceTypeException;

/**
 * @psalm-consistent-constructor
 */
abstract class AbstractResource extends AbstractJsonSerializable implements ResourceInterface
{
    protected string $object = '';
    protected string $id = '';
    private array $rawData = [];

    public function __construct()
    {
    }

    /**
     * @throws InvalidResourceTypeException
     * @throws InvalidResourceException
     */
    public static function fromRawData(array $rawData): self
    {
        $resource = new static();
        $resource->setRawData($rawData);

        if (!isset($rawData['object'])) {
            throw new InvalidResourceException();
        }

        if ($resource->rawData['object'] !== $resource->getResourceType()) {
            throw new InvalidResourceTypeException((string) $resource->rawData['object']);
        }

        $resource->initialize();

        return $resource;
    }

    abstract protected function initialize(): void;

    abstract public static function getResourceType(): string;

    /**
     * @return array
     */
    public function getRawData(): array
    {
        return $this->rawData;
    }

    protected function setRawData(array $rawData): self
    {
        $this->rawData = $rawData;

        $this->object = (string) ($this->getRawData()['object'] ?? '');
        $this->id = (string) ($this->getRawData()['id'] ?? '');

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

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): string
    {
        return $this->id;
    }
}

<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource;

use Brd6\NotionSdkPhp\Exception\InvalidResourceTypeException;
use JsonSerializable;

use function array_filter;
use function get_object_vars;

use const ARRAY_FILTER_USE_KEY;

abstract class AbstractResource implements JsonSerializable
{
    protected string $object = '';
    protected string $id = '';
    private array $responseData = [];

    final public function __construct()
    {
    }

    /**
     * @throws InvalidResourceTypeException
     */
    public static function fromResponseData(array $responseData): self
    {
        $resource = new static();
        $resource->setResponseData($responseData);

        if (
            !isset($resource->responseData['object']) ||
            $resource->responseData['object'] !== $resource->getResourceType()
        ) {
            throw new InvalidResourceTypeException((string) $resource->responseData['object']);
        }

        $resource->initialize();

        return $resource;
    }

    abstract protected function initialize(): void;

    abstract public static function getResourceType(): string;

    /**
     * @return array
     */
    public function getResponseData(): array
    {
        return $this->responseData;
    }

    public function jsonSerialize(): array
    {
        return array_filter(get_object_vars($this), fn (string $key) => $key !== 'responseData', ARRAY_FILTER_USE_KEY);
    }

    protected function setResponseData(array $responseData): self
    {
        $this->responseData = $responseData;

        $this->object = (string) ($this->getResponseData()['object'] ?? '');
        $this->id = (string) ($this->getResponseData()['id'] ?? '');

        return $this;
    }

    public function getObject(): string
    {
        return $this->object;
    }

    public function getId(): string
    {
        return $this->id;
    }
}

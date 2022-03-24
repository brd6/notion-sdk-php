<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Pagination;

use Brd6\NotionSdkPhp\Exception\InvalidPaginationResponseException;
use Brd6\NotionSdkPhp\Exception\UnsupportedPaginationResponseTypeException;
use Brd6\NotionSdkPhp\Resource\AbstractJsonSerializable;
use Brd6\NotionSdkPhp\Util\StringHelper;

use function class_exists;

abstract class AbstractPaginationResponse extends AbstractJsonSerializable
{
    protected bool $hasMore = false;
    protected ?string $nextCursor = null;
    protected array $results = [];
    protected string $object = '';
    protected string $type = '';
    private array $rawData = [];

    /**
     * @throws UnsupportedPaginationResponseTypeException
     * @throws InvalidPaginationResponseException
     */
    public static function fromRawData(array $rawData): self
    {
        if (
            !isset($rawData['object']) ||
            !isset($rawData['type'])
        ) {
            throw new InvalidPaginationResponseException();
        }

        $class = static::getMapClassFromType((string) $rawData['type']);

        /** @var static $paginationResponse */
        $paginationResponse = new $class();

        $paginationResponse
            ->setRawData($rawData)
            ->initialize();

        return $paginationResponse;
    }

    /**
     * @throws UnsupportedPaginationResponseTypeException
     */
    protected static function getMapClassFromType(string $type): string
    {
        $typeFormatted = StringHelper::snakeCaseToCamelCase($type);
        $class = "Brd6\\NotionSdkPhp\\Resource\Pagination\\${typeFormatted}Response";

        if (!class_exists($class)) {
            throw new UnsupportedPaginationResponseTypeException($type);
        }

        return $class;
    }

    abstract protected function initialize(): void;

    public function isHasMore(): bool
    {
        return $this->hasMore;
    }

    public function setHasMore(bool $hasMore): self
    {
        $this->hasMore = $hasMore;

        return $this;
    }

    public function getNextCursor(): ?string
    {
        return $this->nextCursor;
    }

    public function setNextCursor(?string $nextCursor): self
    {
        $this->nextCursor = $nextCursor;

        return $this;
    }

    public function getResults(): array
    {
        return $this->results;
    }

    public function setResults(array $results): self
    {
        $this->results = $results;

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

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getRawData(): array
    {
        return $this->rawData;
    }

    public function setRawData(array $rawData): self
    {
        $this->rawData = $rawData;

        $this->object = (string) ($this->getRawData()['object'] ?? '');
        $this->type = (string) ($this->getRawData()['type'] ?? '');
        $this->nextCursor = isset($this->getRawData()['next_cursor']) ?
            ((string) $this->getRawData()['next_cursor']) :
            null;
        $this->hasMore = (bool) $this->getRawData()['next_cursor'];

        return $this;
    }
}

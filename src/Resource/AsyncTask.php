<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource;

use DateTimeImmutable;
use Exception;

use function in_array;

class AsyncTask extends AbstractResource
{
    public const RESOURCE_TYPE = 'async_task';

    public const STATUS_QUEUED = 'queued';
    public const STATUS_RUNNING = 'running';
    public const STATUS_RETRYING = 'retrying';
    public const STATUS_SUCCEEDED = 'succeeded';
    public const STATUS_FAILED = 'failed';

    protected string $status = '';
    protected ?string $statusUrl = null;
    protected ?DateTimeImmutable $createdTime = null;
    protected ?int $pollAfterSeconds = null;
    protected array $operation = [];
    protected array $result = [];
    protected array $error = [];

    public function __construct()
    {
        parent::__construct();

        $this->object = self::RESOURCE_TYPE;
    }

    public static function getResourceType(): string
    {
        return self::RESOURCE_TYPE;
    }

    /**
     * @throws Exception
     */
    protected function initialize(): void
    {
        $rawData = $this->getRawData();

        $this->status = (string) ($rawData['status'] ?? '');
        $this->statusUrl = isset($rawData['status_url']) ? (string) $rawData['status_url'] : null;
        $this->createdTime = isset($rawData['created_time']) ?
            new DateTimeImmutable((string) $rawData['created_time']) :
            null;
        $this->pollAfterSeconds = isset($rawData['poll_after_seconds']) ? (int) $rawData['poll_after_seconds'] : null;
        $this->operation = (array) ($rawData['operation'] ?? []);
        $this->result = (array) ($rawData['result'] ?? []);
        $this->error = (array) ($rawData['error'] ?? []);
    }

    public function isTerminal(): bool
    {
        return in_array($this->status, [self::STATUS_SUCCEEDED, self::STATUS_FAILED], true);
    }

    public function isSucceeded(): bool
    {
        return $this->status === self::STATUS_SUCCEEDED;
    }

    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getStatusUrl(): ?string
    {
        return $this->statusUrl;
    }

    public function setStatusUrl(?string $statusUrl): self
    {
        $this->statusUrl = $statusUrl;

        return $this;
    }

    public function getCreatedTime(): ?DateTimeImmutable
    {
        return $this->createdTime;
    }

    public function setCreatedTime(?DateTimeImmutable $createdTime): self
    {
        $this->createdTime = $createdTime;

        return $this;
    }

    public function getPollAfterSeconds(): ?int
    {
        return $this->pollAfterSeconds;
    }

    public function setPollAfterSeconds(?int $pollAfterSeconds): self
    {
        $this->pollAfterSeconds = $pollAfterSeconds;

        return $this;
    }

    public function getOperation(): array
    {
        return $this->operation;
    }

    public function setOperation(array $operation): self
    {
        $this->operation = $operation;

        return $this;
    }

    public function getResult(): array
    {
        return $this->result;
    }

    public function setResult(array $result): self
    {
        $this->result = $result;

        return $this;
    }

    public function getError(): array
    {
        return $this->error;
    }

    public function setError(array $error): self
    {
        $this->error = $error;

        return $this;
    }
}

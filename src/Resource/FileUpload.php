<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource;

use DateTimeImmutable;
use Exception;

class FileUpload extends AbstractResource
{
    public const RESOURCE_TYPE = 'file_upload';

    public const STATUS_PENDING = 'pending';
    public const STATUS_UPLOADED = 'uploaded';
    public const STATUS_EXPIRED = 'expired';
    public const STATUS_FAILED = 'failed';

    protected ?DateTimeImmutable $createdTime = null;
    protected array $createdBy = [];
    protected ?DateTimeImmutable $lastEditedTime = null;
    protected ?DateTimeImmutable $expiryTime = null;
    protected string $status = '';
    protected ?string $filename = null;
    protected ?string $contentType = null;
    protected ?int $contentLength = null;
    protected ?string $uploadUrl = null;
    protected ?string $completeUrl = null;
    protected array $numberOfParts = [];
    protected array $fileImportResult = [];
    protected bool $inTrash = false;

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

        $this->createdTime = isset($rawData['created_time']) ?
            new DateTimeImmutable((string) $rawData['created_time']) :
            null;
        $this->createdBy = (array) ($rawData['created_by'] ?? []);
        $this->lastEditedTime = isset($rawData['last_edited_time']) ?
            new DateTimeImmutable((string) $rawData['last_edited_time']) :
            null;
        $this->expiryTime = isset($rawData['expiry_time']) ?
            new DateTimeImmutable((string) $rawData['expiry_time']) :
            null;
        $this->status = (string) ($rawData['status'] ?? '');
        $this->filename = isset($rawData['filename']) ? (string) $rawData['filename'] : null;
        $this->contentType = isset($rawData['content_type']) ? (string) $rawData['content_type'] : null;
        $this->contentLength = isset($rawData['content_length']) ? (int) $rawData['content_length'] : null;
        $this->uploadUrl = isset($rawData['upload_url']) ? (string) $rawData['upload_url'] : null;
        $this->completeUrl = isset($rawData['complete_url']) ? (string) $rawData['complete_url'] : null;
        $this->numberOfParts = (array) ($rawData['number_of_parts'] ?? []);
        $this->fileImportResult = (array) ($rawData['file_import_result'] ?? []);
        $this->inTrash = (bool) ($rawData['in_trash'] ?? $rawData['archived'] ?? false);
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

    public function getCreatedBy(): array
    {
        return $this->createdBy;
    }

    public function setCreatedBy(array $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getLastEditedTime(): ?DateTimeImmutable
    {
        return $this->lastEditedTime;
    }

    public function setLastEditedTime(?DateTimeImmutable $lastEditedTime): self
    {
        $this->lastEditedTime = $lastEditedTime;

        return $this;
    }

    public function getExpiryTime(): ?DateTimeImmutable
    {
        return $this->expiryTime;
    }

    public function setExpiryTime(?DateTimeImmutable $expiryTime): self
    {
        $this->expiryTime = $expiryTime;

        return $this;
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

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(?string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    public function getContentType(): ?string
    {
        return $this->contentType;
    }

    public function setContentType(?string $contentType): self
    {
        $this->contentType = $contentType;

        return $this;
    }

    public function getContentLength(): ?int
    {
        return $this->contentLength;
    }

    public function setContentLength(?int $contentLength): self
    {
        $this->contentLength = $contentLength;

        return $this;
    }

    public function getUploadUrl(): ?string
    {
        return $this->uploadUrl;
    }

    public function setUploadUrl(?string $uploadUrl): self
    {
        $this->uploadUrl = $uploadUrl;

        return $this;
    }

    public function getCompleteUrl(): ?string
    {
        return $this->completeUrl;
    }

    public function setCompleteUrl(?string $completeUrl): self
    {
        $this->completeUrl = $completeUrl;

        return $this;
    }

    public function getNumberOfParts(): array
    {
        return $this->numberOfParts;
    }

    public function setNumberOfParts(array $numberOfParts): self
    {
        $this->numberOfParts = $numberOfParts;

        return $this;
    }

    public function getFileImportResult(): array
    {
        return $this->fileImportResult;
    }

    public function setFileImportResult(array $fileImportResult): self
    {
        $this->fileImportResult = $fileImportResult;

        return $this;
    }

    public function isInTrash(): bool
    {
        return $this->inTrash;
    }

    public function setInTrash(bool $inTrash): self
    {
        $this->inTrash = $inTrash;

        return $this;
    }
}

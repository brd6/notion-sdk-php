<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\FileUpload;

use Brd6\NotionSdkPhp\Resource\AbstractJsonSerializable;

class FileUploadRequest extends AbstractJsonSerializable
{
    public const MODE_SINGLE_PART = 'single_part';
    public const MODE_MULTI_PART = 'multi_part';
    public const MODE_EXTERNAL_URL = 'external_url';

    protected ?string $mode = null;
    protected ?string $filename = null;
    protected ?string $contentType = null;
    protected ?int $numberOfParts = null;
    protected ?string $externalUrl = null;

    public function getMode(): ?string
    {
        return $this->mode;
    }

    public function setMode(?string $mode): self
    {
        $this->mode = $mode;

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

    public function getNumberOfParts(): ?int
    {
        return $this->numberOfParts;
    }

    public function setNumberOfParts(?int $numberOfParts): self
    {
        $this->numberOfParts = $numberOfParts;

        return $this;
    }

    public function getExternalUrl(): ?string
    {
        return $this->externalUrl;
    }

    public function setExternalUrl(?string $externalUrl): self
    {
        $this->externalUrl = $externalUrl;

        return $this;
    }
}

<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Property;

class EmbedProperty extends AbstractProperty
{
    public const TYPE_FILE_UPLOAD = 'file_upload';

    protected string $url = '';
    protected ?string $type = null;
    protected ?FileUploadProperty $fileUpload = null;

    /**
     * Embeds an uploaded file — for example an uploaded .html file, which Notion
     * renders as an HTML block. Responses always carry a temporary signed `url`
     * instead of the `file_upload` reference.
     */
    public static function fromFileUpload(string $fileUploadId): self
    {
        $property = new self();

        $property->type = self::TYPE_FILE_UPLOAD;
        $property->fileUpload = (new FileUploadProperty())->setId($fileUploadId);

        return $property;
    }

    public static function fromRawData(array $rawData): self
    {
        $property = new self();

        $property->url = (string) ($rawData['url'] ?? '');
        $property->type = isset($rawData['type']) ? (string) $rawData['type'] : null;
        $property->fileUpload = isset($rawData['file_upload']) ?
            FileUploadProperty::fromRawData((array) $rawData['file_upload']) :
            null;

        return $property;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getFileUpload(): ?FileUploadProperty
    {
        return $this->fileUpload;
    }

    public function setFileUpload(?FileUploadProperty $fileUpload): self
    {
        $this->fileUpload = $fileUpload;

        return $this;
    }
}

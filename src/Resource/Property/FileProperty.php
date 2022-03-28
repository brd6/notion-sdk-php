<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Property;

use DateTimeImmutable;

class FileProperty extends AbstractProperty
{
    protected string $url = '';
    protected ?DateTimeImmutable $expiryTime = null;

    public static function fromRawData(array $rawData): self
    {
        $property = new self();

        $property->url = (string) $rawData['url'];
        $property->expiryTime = new DateTimeImmutable((string) $rawData['expiry_time']);

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

    public function getExpiryTime(): ?DateTimeImmutable
    {
        return $this->expiryTime;
    }

    public function setExpiryTime(?DateTimeImmutable $expiryTime): self
    {
        $this->expiryTime = $expiryTime;

        return $this;
    }
}

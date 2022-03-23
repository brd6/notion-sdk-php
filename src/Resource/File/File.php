<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\File;

use DateTimeImmutable;

class File extends AbstractFile
{
    public const FILE_TYPE = 'file';

    protected string $url = '';
    protected ?DateTimeImmutable $expiryTime = null;

    public static function getFileType(): string
    {
        return self::FILE_TYPE;
    }

    protected function initialize(): void
    {
        $data = (array) $this->getRawData()[$this->getType()];
        $this->url = (string) $data['url'];
        $this->expiryTime = new DateTimeImmutable((string) $data['expiry_time']);
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

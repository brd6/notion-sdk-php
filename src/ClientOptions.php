<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp;

use Psr\Http\Client\ClientInterface as HttpClientInterface;

use function strlen;

class ClientOptions
{
    public const DEFAULT_BASE_URL = 'https://api.notion.com/v1/';
    public const DEFAULT_NOTION_VERSION = '2022-02-22';
    public const DEFAULT_TIMEOUT = 60; // in seconds

    private string $auth = '';
    private string $baseUrl = self::DEFAULT_BASE_URL;
    private string $notionVersion = self::DEFAULT_NOTION_VERSION;
    private int $timeout = self::DEFAULT_TIMEOUT;
    private ?HttpClientInterface $httpClient = null;

    public function getAuth(): string
    {
        return $this->auth;
    }

    public function setAuth(string $auth): self
    {
        $this->auth = $auth;

        return $this;
    }

    public function hasAuth(): bool
    {
        return strlen($this->auth) > 0;
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    public function setBaseUrl(string $baseUrl): self
    {
        $this->baseUrl = $baseUrl;

        return $this;
    }

    public function getNotionVersion(): string
    {
        return $this->notionVersion;
    }

    public function setNotionVersion(string $notionVersion): self
    {
        $this->notionVersion = $notionVersion;

        return $this;
    }

    public function getTimeout(): int
    {
        return $this->timeout;
    }

    public function setTimeout(int $timeout): self
    {
        $this->timeout = $timeout;

        return $this;
    }

    public function getHttpClient(): ?HttpClientInterface
    {
        return $this->httpClient;
    }

    public function setHttpClient(HttpClientInterface $httpClient): self
    {
        $this->httpClient = $httpClient;

        return $this;
    }
}

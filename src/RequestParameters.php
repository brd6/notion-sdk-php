<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp;

class RequestParameters
{
    private string $path = '';
    private string $method = '';
    private array $query = [];
    private array $body = [];
    private ?string $rawBody = null;

    /**
     * @var array<string, string>
     */
    private array $headers = [];

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function setMethod(string $method): self
    {
        $this->method = $method;

        return $this;
    }

    public function getQuery(): array
    {
        return $this->query;
    }

    /**
     * @param array $query
     *
     * @return RequestParameters
     */
    public function setQuery(array $query): self
    {
        $this->query = $query;

        return $this;
    }

    public function getBody(): array
    {
        return $this->body;
    }

    /**
     * @param array $body
     *
     * @return RequestParameters
     */
    public function setBody(array $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function getRawBody(): ?string
    {
        return $this->rawBody;
    }

    /**
     * Takes precedence over the JSON-encoded `body` when set.
     */
    public function setRawBody(?string $rawBody): self
    {
        $this->rawBody = $rawBody;

        return $this;
    }

    /**
     * @return array<string, string>
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @param array<string, string> $headers
     *
     * @return RequestParameters
     */
    public function setHeaders(array $headers): self
    {
        $this->headers = $headers;

        return $this;
    }
}

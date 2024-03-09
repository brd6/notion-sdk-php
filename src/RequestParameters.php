<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp;

class RequestParameters
{
    private string $path = '';
    private string $method = '';
    private array $query = [];
    private array $body = [];

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
}

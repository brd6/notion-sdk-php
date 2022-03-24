<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource;

class Link
{
    protected string $type = '';
    protected string $url = '';

    public static function fromRawData(array $rawData): self
    {
        $link = new self();
        $link->type = (string) ($rawData['type'] ?? '');
        $link->url = (string) $rawData['url'];

        return $link;
    }

    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return Link
     */
    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     *
     * @return Link
     */
    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }
}

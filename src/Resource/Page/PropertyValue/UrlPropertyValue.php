<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Page\PropertyValue;

class UrlPropertyValue extends AbstractPropertyValue
{
    protected string $url = '';

    protected function initialize(): void
    {
        $this->url = (string) ($this->getRawData()[$this->getType()] ?? '');
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
}

<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Page\PropertyValue;

use Brd6\NotionSdkPhp\Resource\Page\AbstractPropertyValue;

class UrlPropertyValue extends AbstractPropertyValue
{
    protected string $url = '';

    protected function initialize(): void
    {
        $data = (array) $this->getRawData()[$this->getType()];
        $this->url = (string) ($data['url'] ?? '');
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

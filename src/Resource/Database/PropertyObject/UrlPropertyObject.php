<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Database\PropertyObject;

use Brd6\NotionSdkPhp\Resource\Database\PropertyConfiguration\UrlPropertyConfiguration;

class UrlPropertyObject extends AbstractPropertyObject
{
    protected ?UrlPropertyConfiguration $url = null;

    public function __construct()
    {
        $this->url = new UrlPropertyConfiguration();
    }

    protected function initialize(): void
    {
        $this->url = isset($this->getRawData()['url']) ?
            UrlPropertyConfiguration::fromRawData((array) $this->getRawData()['url']) :
            null;
    }

    public function getUrl(): ?UrlPropertyConfiguration
    {
        return $this->url;
    }

    public function setUrl(?UrlPropertyConfiguration $url): self
    {
        $this->url = $url;

        return $this;
    }
}

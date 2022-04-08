<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Database\PropertyObject;

use Brd6\NotionSdkPhp\Resource\Database\PropertyConfiguration\TitlePropertyConfiguration;

class TitlePropertyObject extends AbstractPropertyObject
{
    protected ?TitlePropertyConfiguration $title = null;

    public function __construct()
    {
        $this->title = new TitlePropertyConfiguration();
    }

    protected function initialize(): void
    {
        $this->title = isset($this->getRawData()['title']) ?
            TitlePropertyConfiguration::fromRawData((array) $this->getRawData()['title']) :
            null;
    }

    public function getTitle(): ?TitlePropertyConfiguration
    {
        return $this->title;
    }

    public function setTitle(?TitlePropertyConfiguration $title): self
    {
        $this->title = $title;

        return $this;
    }
}

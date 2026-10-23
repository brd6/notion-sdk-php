<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Database\PropertyObject;

use Brd6\NotionSdkPhp\Resource\Database\PropertyConfiguration\ButtonPropertyConfiguration;

class ButtonPropertyObject extends AbstractPropertyObject
{
    protected ?ButtonPropertyConfiguration $button = null;

    public function __construct()
    {
        $this->button = new ButtonPropertyConfiguration();
    }

    protected function initialize(): void
    {
        $this->button = isset($this->getRawData()['button']) ?
            ButtonPropertyConfiguration::fromRawData((array) $this->getRawData()['button']) :
            null;
    }

    public function getButton(): ?ButtonPropertyConfiguration
    {
        return $this->button;
    }

    public function setButton(?ButtonPropertyConfiguration $button): self
    {
        $this->button = $button;

        return $this;
    }
}

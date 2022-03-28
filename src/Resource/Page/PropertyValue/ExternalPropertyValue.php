<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Page\PropertyValue;

class ExternalPropertyValue extends AbstractFilePropertyValue
{
    protected string $external = '';

    protected function initialize(): void
    {
        $this->external = (string) $this->getRawData()[$this->getType()];
    }

    public function getExternal(): string
    {
        return $this->external;
    }

    public function setExternal(string $external): self
    {
        $this->external = $external;

        return $this;
    }
}

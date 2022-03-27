<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Property\Value;

class StringValueProperty extends AbstractValueProperty
{
    protected ?string $string = null;

    protected function initialize(): void
    {
        $this->string = isset($this->getRawData()['string']) ?
            (string) $this->getRawData()['string'] :
            null;
    }

    public function getString(): ?string
    {
        return $this->string;
    }

    public function setString(string $string): self
    {
        $this->string = $string;

        return $this;
    }
}

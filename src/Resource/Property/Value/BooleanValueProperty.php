<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Property\Value;

class BooleanValueProperty extends AbstractValueProperty
{
    protected bool $boolean = false;

    protected function initialize(): void
    {
        $this->boolean = (bool) $this->getRawData()['boolean'];
    }

    public function getBoolean(): bool
    {
        return $this->boolean;
    }

    public function setBoolean(bool $boolean): self
    {
        $this->boolean = $boolean;

        return $this;
    }
}

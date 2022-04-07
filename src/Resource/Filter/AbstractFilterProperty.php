<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Filter;

use Brd6\NotionSdkPhp\Resource\AbstractJsonSerializable;

abstract class AbstractFilterProperty extends AbstractJsonSerializable
{
    protected string $property = '';

    public function getProperty(): string
    {
        return $this->property;
    }

    public function setProperty(string $property): self
    {
        $this->property = $property;

        return $this;
    }
}

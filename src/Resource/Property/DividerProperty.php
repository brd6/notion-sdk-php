<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Property;

class DividerProperty extends AbstractProperty
{
    public static function fromRawData(array $rawData): self
    {
        return new self();
    }
}

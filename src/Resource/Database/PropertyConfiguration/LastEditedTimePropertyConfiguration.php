<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Database\PropertyConfiguration;

use Brd6\NotionSdkPhp\Resource\Property\AbstractProperty;

class LastEditedTimePropertyConfiguration extends AbstractProperty
{
    public static function fromRawData(array $rawData): self
    {
        return new self();
    }
}

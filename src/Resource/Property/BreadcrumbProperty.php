<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Property;

use Brd6\NotionSdkPhp\Resource\AbstractProperty;

class BreadcrumbProperty extends AbstractProperty
{
    public static function fromRawData(array $rawData): self
    {
        return new self();
    }
}

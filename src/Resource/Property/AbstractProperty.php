<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Property;

use Brd6\NotionSdkPhp\Resource\AbstractJsonSerializable;

abstract class AbstractProperty extends AbstractJsonSerializable
{
    /** @var string */
    public const PROPERTY_BASE_TYPE = '';

    abstract public static function fromRawData(array $rawData): self;
}

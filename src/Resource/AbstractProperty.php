<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource;

abstract class AbstractProperty extends AbstractJsonSerializable
{
    abstract public static function fromRawData(array $rawData): self;
}

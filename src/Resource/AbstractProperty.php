<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource;

abstract class AbstractProperty
{
    abstract public static function fromRawData(array $rawData): self;
}

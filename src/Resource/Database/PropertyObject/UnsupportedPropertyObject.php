<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Database\PropertyObject;

class UnsupportedPropertyObject extends AbstractPropertyObject
{
    protected function initialize(): void
    {
    }

    public function isWritable(): bool
    {
        return false;
    }
}

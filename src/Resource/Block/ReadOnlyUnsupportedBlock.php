<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Block;

use Brd6\NotionSdkPhp\Exception\UnsupportedPropertyTypeException;

final class ReadOnlyUnsupportedBlock extends UnsupportedBlock
{
    public function propertyToArray(): array
    {
        throw new UnsupportedPropertyTypeException($this->getType(), self::RESOURCE_TYPE);
    }

    public function toArrayForCreate(): array
    {
        throw new UnsupportedPropertyTypeException($this->getType(), self::RESOURCE_TYPE);
    }
}

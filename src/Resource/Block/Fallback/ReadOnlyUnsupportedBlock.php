<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Block\Fallback;

use Brd6\NotionSdkPhp\Exception\UnsupportedPropertyTypeException;
use Brd6\NotionSdkPhp\Resource\Block\UnsupportedBlock;

final class ReadOnlyUnsupportedBlock extends UnsupportedBlock
{
    public function jsonSerialize(): array
    {
        throw new UnsupportedPropertyTypeException($this->getType(), self::RESOURCE_TYPE);
    }

    public function propertyToArray(): array
    {
        throw new UnsupportedPropertyTypeException($this->getType(), self::RESOURCE_TYPE);
    }

    public function toArrayForCreate(): array
    {
        throw new UnsupportedPropertyTypeException($this->getType(), self::RESOURCE_TYPE);
    }
}

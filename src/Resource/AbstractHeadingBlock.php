<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource;

use Brd6\NotionSdkPhp\Resource\Property\HeadingProperty;
use Brd6\NotionSdkPhp\Util\StringHelper;

abstract class AbstractHeadingBlock extends AbstractBlock
{
    protected function initializeBlockProperty(): void
    {
        $data = (array) $this->getRawData()[$this->getType()];

        $typeFormatted = StringHelper::snakeCaseToCamelCase($this->getType());
        $methodName = "set$typeFormatted";

        $this->$methodName(HeadingProperty::fromRawData($data));
    }
}

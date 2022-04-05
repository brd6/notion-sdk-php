<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Block;

use Brd6\NotionSdkPhp\Exception\InvalidResourceException;
use Brd6\NotionSdkPhp\Exception\InvalidResourceTypeException;
use Brd6\NotionSdkPhp\Exception\InvalidRichTextException;
use Brd6\NotionSdkPhp\Exception\UnsupportedRichTextTypeException;
use Brd6\NotionSdkPhp\Exception\UnsupportedUserTypeException;
use Brd6\NotionSdkPhp\Resource\Property\HeadingProperty;
use Brd6\NotionSdkPhp\Util\StringHelper;

abstract class AbstractHeadingBlock extends AbstractBlock
{
    /**
     * @throws UnsupportedUserTypeException
     * @throws InvalidResourceTypeException
     * @throws UnsupportedRichTextTypeException
     * @throws InvalidResourceException
     * @throws InvalidRichTextException
     */
    protected function initializeBlockProperty(): void
    {
        $data = (array) $this->getRawData()[$this->getType()];

        $typeFormatted = StringHelper::snakeCaseToCamelCase($this->getType());
        $methodName = "set$typeFormatted";

        $this->$methodName(HeadingProperty::fromRawData($data));
    }
}

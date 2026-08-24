<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Page\PropertyItem;

use Brd6\NotionSdkPhp\Exception\UnsupportedNotionExceptionInterface;
use Brd6\NotionSdkPhp\Resource\Page\PropertyItem\Fallback\UnsupportedPropertyItem;

abstract class ForwardCompatiblePropertyItemFactory extends AbstractPropertyItem
{
    public static function create(array $rawData): AbstractPropertyItem
    {
        try {
            return AbstractPropertyItem::fromRawData($rawData);
        } catch (UnsupportedNotionExceptionInterface $exception) {
            $propertyItem = new UnsupportedPropertyItem();
            $propertyItem
                ->setRawData($rawData)
                ->initialize();

            return $propertyItem;
        }
    }
}

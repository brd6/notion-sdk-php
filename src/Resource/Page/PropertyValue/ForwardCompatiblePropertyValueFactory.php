<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Page\PropertyValue;

use Brd6\NotionSdkPhp\Exception\UnsupportedNotionExceptionInterface;

final class ForwardCompatiblePropertyValueFactory extends AbstractPropertyValue
{
    public static function create(array $rawData): AbstractPropertyValue
    {
        try {
            return AbstractPropertyValue::fromRawData($rawData);
        } catch (UnsupportedNotionExceptionInterface $exception) {
            $propertyValue = new UnsupportedPropertyValue();
            $propertyValue
                ->setRawData($rawData)
                ->initialize();

            return $propertyValue;
        }
    }

    protected function initialize(): void
    {
    }
}

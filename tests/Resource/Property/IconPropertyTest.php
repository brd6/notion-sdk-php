<?php

declare(strict_types=1);

namespace Brd6\Test\NotionSdkPhp\Resource\Property;

use Brd6\NotionSdkPhp\Resource\Property\IconProperty;
use Brd6\Test\NotionSdkPhp\TestCase;

class IconPropertyTest extends TestCase
{
    public function testFromRawData(): void
    {
        $property = IconProperty::fromRawData([
            'name' => 'book',
            'color' => 'gray',
        ]);

        $this->assertSame('book', $property->getName());
        $this->assertSame('gray', $property->getColor());
    }

    public function testFromRawDataWithMissingValues(): void
    {
        $property = IconProperty::fromRawData([]);

        $this->assertSame('', $property->getName());
        $this->assertSame('', $property->getColor());
        $this->assertSame('', $property->getUrl());
    }

    public function testGetUrl(): void
    {
        $property = IconProperty::fromRawData([
            'name' => 'add',
            'color' => 'blue',
        ]);

        $this->assertSame(
            'https://www.notion.so/icons/add_blue.svg?mode=light',
            $property->getUrl(),
        );
        $this->assertSame(
            'https://www.notion.so/icons/add_blue.svg?mode=dark',
            $property->getUrl('dark'),
        );
        $this->assertSame(
            'https://www.notion.so/icons/add_blue.svg?mode=light',
            $property->getUrl('unsupported'),
        );
    }

    public function testToArrayRoundTrip(): void
    {
        $rawData = [
            'name' => 'book',
            'color' => 'gray',
        ];

        $property = IconProperty::fromRawData($rawData);

        $this->assertSame($rawData, $property->toArray());
    }
}

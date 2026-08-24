<?php

declare(strict_types=1);

namespace Brd6\Test\NotionSdkPhp\Resource\Page\PropertyValue;

use Brd6\NotionSdkPhp\Exception\InvalidFileException;
use Brd6\NotionSdkPhp\Exception\InvalidPropertyValueException;
use Brd6\NotionSdkPhp\Resource\Page;
use Brd6\NotionSdkPhp\Resource\Page\PropertyValue\AbstractPropertyValue;
use Brd6\NotionSdkPhp\Resource\Page\PropertyValue\UnsupportedPropertyValue;
use PHPUnit\Framework\TestCase;

use function count;
use function file_get_contents;
use function json_decode;

class UnsupportedPropertyValueTest extends TestCase
{
    public function testUnknownPropertyValueFallsBackAndRetainsRawData(): void
    {
        $rawData = [
            'id' => 'future-id',
            'type' => 'future_property',
            'future_property' => ['value' => 'future-value'],
        ];

        $property = AbstractPropertyValue::fromRawData($rawData);

        $this->assertInstanceOf(UnsupportedPropertyValue::class, $property);
        $this->assertSame('future_property', $property->getType());
        $this->assertSame('future-id', $property->getId());
        $this->assertSame($rawData, $property->getRawData());
    }

    public function testPageHydratesUnknownPropertyValue(): void
    {
        $rawData = $this->getPageRawData();
        $rawData['properties']['Future'] = [
            'id' => 'future-id',
            'type' => 'future_property',
            'future_property' => ['value' => 'future-value'],
        ];

        /** @var Page $page */
        $page = Page::fromRawData($rawData);

        $this->assertInstanceOf(
            UnsupportedPropertyValue::class,
            $page->getProperties()['Future'],
        );
        $this->assertGreaterThan(1, count($page->getProperties()));
    }

    public function testUnknownPropertyValueCollidingWithAbstractClassFallsBack(): void
    {
        $property = AbstractPropertyValue::fromRawData([
            'id' => 'abstract-id',
            'type' => 'abstract',
            'abstract' => [],
        ]);

        $this->assertInstanceOf(UnsupportedPropertyValue::class, $property);
        $this->assertSame('abstract', $property->getType());
    }

    public function testKnownPropertyWithUnsupportedNestedFeatureFallsBack(): void
    {
        $rawData = [
            'id' => 'files-id',
            'type' => 'files',
            'files' => [
                [
                    'type' => 'future_file',
                    'future_file' => [],
                ],
            ],
        ];

        $property = AbstractPropertyValue::fromRawData($rawData);

        $this->assertInstanceOf(UnsupportedPropertyValue::class, $property);
        $this->assertSame('files', $property->getType());
        $this->assertSame($rawData, $property->getRawData());
    }

    public function testInvalidPropertyValueStillThrows(): void
    {
        $this->expectException(InvalidPropertyValueException::class);

        AbstractPropertyValue::fromRawData(['id' => 'missing-type']);
    }

    public function testKnownPropertyWithInvalidNestedFeatureStillThrows(): void
    {
        $this->expectException(InvalidFileException::class);

        AbstractPropertyValue::fromRawData([
            'id' => 'files-id',
            'type' => 'files',
            'files' => [
                ['name' => 'missing-type'],
            ],
        ]);
    }

    private function getPageRawData(): array
    {
        return (array) json_decode(
            (string) file_get_contents('tests/Fixtures/client_pages_retrieve_page_200.json'),
            true,
        );
    }
}

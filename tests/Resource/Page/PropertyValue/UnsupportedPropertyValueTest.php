<?php

declare(strict_types=1);

namespace Brd6\Test\NotionSdkPhp\Resource\Page\PropertyValue;

use Brd6\NotionSdkPhp\Exception\AbstractNotionException;
use Brd6\NotionSdkPhp\Exception\InvalidFileException;
use Brd6\NotionSdkPhp\Exception\InvalidPropertyValueException;
use Brd6\NotionSdkPhp\Exception\UnsupportedFileTypeException;
use Brd6\NotionSdkPhp\Exception\UnsupportedNotionExceptionInterface;
use Brd6\NotionSdkPhp\Exception\UnsupportedPropertyValueException;
use Brd6\NotionSdkPhp\Resource\Page;
use Brd6\NotionSdkPhp\Resource\Page\PropertyValue\AbstractPropertyValue;
use Brd6\NotionSdkPhp\Resource\Page\PropertyValue\UnsupportedPropertyValue;
use PHPUnit\Framework\TestCase;

use function count;
use function file_get_contents;
use function get_parent_class;
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

        $property = AbstractPropertyValue::fromRawDataWithUnsupportedContentFallback($rawData);

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
        $page = Page::fromRawDataWithUnsupportedContentFallback($rawData);

        $this->assertInstanceOf(
            UnsupportedPropertyValue::class,
            $page->getProperties()['Future'],
        );
        $this->assertGreaterThan(1, count($page->getProperties()));
    }

    public function testPageOmitsUnknownPropertyValueFromWrites(): void
    {
        $rawData = $this->getPageRawData();
        $rawData['properties']['Future'] = [
            'id' => 'future-id',
            'type' => 'future_property',
            'future_property' => ['value' => 'future-value'],
        ];

        $page = Page::fromRawDataWithUnsupportedContentFallback($rawData);

        $this->assertArrayNotHasKey('Future', $page->toArrayForCreate()['properties']);
        $this->assertArrayNotHasKey('Future', $page->toArrayForUpdate()['properties']);
    }

    public function testPageFallbackSkipsUnsupportedTopLevelContent(): void
    {
        $rawData = $this->getPageRawData();
        $rawData['created_by']['type'] = 'future_user';
        $rawData['icon'] = [
            'type' => 'future_file',
            'future_file' => [],
        ];
        $rawData['parent'] = [
            'type' => 'future_parent',
            'future_parent' => 'future-id',
        ];

        $page = Page::fromRawDataWithUnsupportedContentFallback($rawData);

        $this->assertNull($page->getCreatedBy());
        $this->assertNotNull($page->getLastEditedBy());
        $this->assertNull($page->getIcon());
        $this->assertNull($page->getParent());
        $this->assertSame($rawData, $page->getRawData());
    }

    public function testUnknownPropertyValueCollidingWithAbstractClassFallsBack(): void
    {
        $property = AbstractPropertyValue::fromRawDataWithUnsupportedContentFallback([
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

        $property = AbstractPropertyValue::fromRawDataWithUnsupportedContentFallback($rawData);

        $this->assertInstanceOf(UnsupportedPropertyValue::class, $property);
        $this->assertSame('files', $property->getType());
        $this->assertSame($rawData, $property->getRawData());
    }

    public function testKnownPropertyWithNestedAbstractClassCollisionFallsBack(): void
    {
        $property = AbstractPropertyValue::fromRawDataWithUnsupportedContentFallback([
            'id' => 'files-id',
            'type' => 'files',
            'files' => [
                [
                    'type' => 'abstract_file',
                    'abstract_file' => [],
                ],
            ],
        ]);

        $this->assertInstanceOf(UnsupportedPropertyValue::class, $property);
        $this->assertSame('files', $property->getType());
    }

    public function testInvalidPropertyValueStillThrows(): void
    {
        $this->expectException(InvalidPropertyValueException::class);

        AbstractPropertyValue::fromRawData(['id' => 'missing-type']);
    }

    public function testUnknownPropertyValueThrowsByDefault(): void
    {
        $this->expectException(UnsupportedPropertyValueException::class);

        AbstractPropertyValue::fromRawData([
            'id' => 'future-id',
            'type' => 'future_property',
            'future_property' => [],
        ]);
    }

    public function testKnownPropertyWithUnsupportedNestedFeatureThrowsByDefault(): void
    {
        $this->expectException(UnsupportedFileTypeException::class);

        AbstractPropertyValue::fromRawData([
            'id' => 'files-id',
            'type' => 'files',
            'files' => [
                [
                    'type' => 'future_file',
                    'future_file' => [],
                ],
            ],
        ]);
    }

    public function testPageThrowsForUnknownPropertyValueByDefault(): void
    {
        $rawData = $this->getPageRawData();
        $rawData['properties']['Future'] = [
            'id' => 'future-id',
            'type' => 'future_property',
            'future_property' => [],
        ];

        $this->expectException(UnsupportedPropertyValueException::class);

        Page::fromRawData($rawData);
    }

    public function testUnsupportedExceptionsKeepTheirParentAndExposeMarkerInterface(): void
    {
        $exception = new UnsupportedPropertyValueException('future_property');

        $this->assertSame(AbstractNotionException::class, get_parent_class($exception));
        $this->assertInstanceOf(UnsupportedNotionExceptionInterface::class, $exception);
    }

    public function testKnownPropertyWithInvalidNestedFeatureStillThrows(): void
    {
        $this->expectException(InvalidFileException::class);

        AbstractPropertyValue::fromRawDataWithUnsupportedContentFallback([
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

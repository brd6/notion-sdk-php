<?php

declare(strict_types=1);

namespace Brd6\Test\NotionSdkPhp\Resource\Page\PropertyValue;

use Brd6\NotionSdkPhp\Exception\AbstractNotionException;
use Brd6\NotionSdkPhp\Exception\InvalidFileException;
use Brd6\NotionSdkPhp\Exception\InvalidPropertyValueException;
use Brd6\NotionSdkPhp\Exception\UnsupportedFileTypeException;
use Brd6\NotionSdkPhp\Exception\UnsupportedNotionExceptionInterface;
use Brd6\NotionSdkPhp\Exception\UnsupportedPropertyValueException;
use Brd6\NotionSdkPhp\Resource\ForwardCompatiblePage;
use Brd6\NotionSdkPhp\Resource\Page;
use Brd6\NotionSdkPhp\Resource\Page\PropertyValue\AbstractPropertyValue;
use Brd6\NotionSdkPhp\Resource\Page\PropertyValue\Fallback\UnsupportedPropertyValue;
use Brd6\NotionSdkPhp\Resource\Page\PropertyValue\ForwardCompatiblePropertyValueFactory;
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

        $property = ForwardCompatiblePropertyValueFactory::create($rawData);

        $this->assertInstanceOf(UnsupportedPropertyValue::class, $property);
        $this->assertSame('future_property', $property->getType());
        $this->assertSame('future-id', $property->getId());
        $this->assertSame($rawData, $property->getRawData());
    }

    public function testPageHydratesUnknownPropertyValue(): void
    {
        $rawData = $this->getPageRawData();
        $rawData['properties']['Future'] = [
            'object' => 'property_item',
            'id' => 'future-id',
            'type' => 'future_property',
            'next_url' => 'https://api.notion.com/future',
            'future_property' => ['value' => 'future-value'],
        ];

        /** @var Page $page */
        $page = ForwardCompatiblePage::fromRawData($rawData);

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
            'object' => 'property_item',
            'id' => 'future-id',
            'type' => 'future_property',
            'next_url' => 'https://api.notion.com/future',
            'future_property' => ['value' => 'future-value'],
        ];

        $page = ForwardCompatiblePage::fromRawData($rawData);

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

        $page = ForwardCompatiblePage::fromRawData($rawData);

        $this->assertNull($page->getCreatedBy());
        $this->assertNotNull($page->getLastEditedBy());
        $this->assertNull($page->getIcon());
        $this->assertNull($page->getParent());
        $this->assertSame($rawData, $page->getRawData());
    }

    public function testUnknownPropertyValueCollidingWithAbstractClassFallsBack(): void
    {
        $property = ForwardCompatiblePropertyValueFactory::create([
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

        $property = ForwardCompatiblePropertyValueFactory::create($rawData);

        $this->assertInstanceOf(UnsupportedPropertyValue::class, $property);
        $this->assertSame('files', $property->getType());
        $this->assertSame($rawData, $property->getRawData());
    }

    public function testKnownPropertyWithNestedAbstractClassCollisionFallsBack(): void
    {
        $property = ForwardCompatiblePropertyValueFactory::create([
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

    public function testFallbackClassNameDoesNotChangeStrictUnsupportedType(): void
    {
        $this->expectException(UnsupportedPropertyValueException::class);

        AbstractPropertyValue::fromRawData([
            'id' => 'unsupported-id',
            'type' => 'unsupported',
            'unsupported' => [],
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

        ForwardCompatiblePropertyValueFactory::create([
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

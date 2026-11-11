<?php

declare(strict_types=1);

namespace Brd6\Test\NotionSdkPhp\Resource\Property\Value;

use Brd6\NotionSdkPhp\Resource\Page;
use Brd6\NotionSdkPhp\Resource\Page\PropertyValue\RollupPropertyValue;
use Brd6\NotionSdkPhp\Resource\Property\Value\ArrayValueProperty;
use Brd6\NotionSdkPhp\Resource\Property\Value\SelectValueProperty;
use Brd6\NotionSdkPhp\Resource\Property\Value\UnsupportedValueProperty;
use Brd6\Test\NotionSdkPhp\TestCase;

use function file_get_contents;
use function json_decode;

class UnsupportedValuePropertyTest extends TestCase
{
    public function testRollupArrayItemsWithoutValueClassFallBackToUnsupportedValueProperty(): void
    {
        $fixture = $this->loadFixture();

        /** @var Page $page */
        $page = Page::fromRawData($fixture);

        /** @var RollupPropertyValue $relatedRollup */
        $relatedRollup = $page->getProperties()['Related Rollup'];

        /** @var ArrayValueProperty $rollupValue */
        $rollupValue = $relatedRollup->getRollup();
        $this->assertInstanceOf(ArrayValueProperty::class, $rollupValue);
        $this->assertSame('show_original', $rollupValue->getFunction());

        $arrayItems = $rollupValue->getArray();
        $this->assertCount(3, $arrayItems);

        $rawArray = (array) $fixture['properties']['Related Rollup']['rollup']['array'];

        $relationItem = $arrayItems[0];
        $this->assertInstanceOf(UnsupportedValueProperty::class, $relationItem);
        $this->assertSame('relation', $relationItem->getType());
        $this->assertSame($rawArray[0], $relationItem->getRawData());

        $titleItem = $arrayItems[1];
        $this->assertInstanceOf(UnsupportedValueProperty::class, $titleItem);
        $this->assertSame('title', $titleItem->getType());
        $this->assertSame($rawArray[1], $titleItem->getRawData());

        $selectItem = $arrayItems[2];
        $this->assertInstanceOf(SelectValueProperty::class, $selectItem);
        $this->assertSame('Drupal 9', $selectItem->getSelect()->getName());
    }

    public function testUnsupportedRollupHydratesAsUnsupportedValueProperty(): void
    {
        $fixture = $this->loadFixture();

        /** @var Page $page */
        $page = Page::fromRawData($fixture);

        /** @var RollupPropertyValue $unsupportedRollup */
        $unsupportedRollup = $page->getProperties()['Unsupported Rollup'];

        $rollupValue = $unsupportedRollup->getRollup();
        $this->assertInstanceOf(UnsupportedValueProperty::class, $rollupValue);
        $this->assertSame('unsupported', $rollupValue->getType());
        $this->assertSame('show_unique', $rollupValue->getFunction());
        $this->assertSame($fixture['properties']['Unsupported Rollup']['rollup'], $rollupValue->getRawData());
    }

    /**
     * @return array<string, mixed>
     */
    private function loadFixture(): array
    {
        return (array) json_decode(
            (string) file_get_contents('tests/Fixtures/rollup_with_relation_values_200.json'),
            true,
        );
    }
}

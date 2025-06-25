<?php

declare(strict_types=1);

namespace Brd6\Test\NotionSdkPhp\Resource\Property\Value;

use Brd6\NotionSdkPhp\Resource\Page;
use Brd6\NotionSdkPhp\Resource\Page\PropertyValue\RollupPropertyValue;
use Brd6\NotionSdkPhp\Resource\Property\Value\ArrayValueProperty;
use Brd6\NotionSdkPhp\Resource\Property\Value\MultiSelectValueProperty;
use Brd6\NotionSdkPhp\Resource\Property\Value\SelectValueProperty;
use Brd6\Test\NotionSdkPhp\TestCase;

use function file_get_contents;
use function json_decode;

class SelectValuePropertyTest extends TestCase
{
    public function testRollupWithSelectValuesWorks(): void
    {
        $fixture = (array) json_decode(
            (string) file_get_contents('tests/Fixtures/rollup_with_select_values_200.json'),
            true,
        );

        /** @var Page $page */
        $page = Page::fromRawData($fixture);

        $this->assertInstanceOf(Page::class, $page);

        /** @var RollupPropertyValue $featuresRollup */
        $featuresRollup = $page->getProperties()['Features Rollup'];
        $this->assertInstanceOf(RollupPropertyValue::class, $featuresRollup);

        /** @var ArrayValueProperty $rollupValue */
        $rollupValue = $featuresRollup->getRollup();
        $this->assertInstanceOf(ArrayValueProperty::class, $rollupValue);

        $arrayItems = $rollupValue->getArray();
        $this->assertCount(2, $arrayItems);

        $firstItem = $arrayItems[0];
        $this->assertInstanceOf(MultiSelectValueProperty::class, $firstItem);
        $this->assertEquals('multi_select', $firstItem->getType());

        $multiSelectOptions = $firstItem->getMultiSelect();
        $this->assertCount(2, $multiSelectOptions);
        $this->assertEquals('Authentication', $multiSelectOptions[0]->getName());
        $this->assertEquals('blue', $multiSelectOptions[0]->getColor());
        $this->assertEquals('Dashboard', $multiSelectOptions[1]->getName());
        $this->assertEquals('green', $multiSelectOptions[1]->getColor());

        /** @var RollupPropertyValue $versionRollup */
        $versionRollup = $page->getProperties()['Version Rollup'];
        $this->assertInstanceOf(RollupPropertyValue::class, $versionRollup);

        /** @var ArrayValueProperty $versionRollupValue */
        $versionRollupValue = $versionRollup->getRollup();
        $this->assertInstanceOf(ArrayValueProperty::class, $versionRollupValue);

        $versionArrayItems = $versionRollupValue->getArray();
        $this->assertCount(2, $versionArrayItems);

        $firstVersionItem = $versionArrayItems[0];
        $this->assertInstanceOf(SelectValueProperty::class, $firstVersionItem);
        $this->assertEquals('select', $firstVersionItem->getType());

        $selectOption = $firstVersionItem->getSelect();
        $this->assertEquals('Drupal 9', $selectOption->getName());
        $this->assertEquals('purple', $selectOption->getColor());
    }
}

<?php

declare(strict_types=1);

namespace Brd6\Test\NotionSdkPhp\Resource\Property\Value;

use Brd6\NotionSdkPhp\Resource\Page;
use Brd6\NotionSdkPhp\Resource\Page\PropertyValue\RollupPropertyValue;
use Brd6\NotionSdkPhp\Resource\Property\Value\UnsupportedValueProperty;
use Brd6\Test\NotionSdkPhp\TestCase;

use function file_get_contents;
use function json_decode;

class UnsupportedValuePropertyTest extends TestCase
{
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
            (string) file_get_contents('tests/Fixtures/rollup_with_property_values_200.json'),
            true,
        );
    }
}

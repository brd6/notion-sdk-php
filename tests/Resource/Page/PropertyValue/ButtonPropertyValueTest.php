<?php

declare(strict_types=1);

namespace Brd6\Test\NotionSdkPhp\Resource\Page\PropertyValue;

use Brd6\NotionSdkPhp\Resource\Page;
use Brd6\NotionSdkPhp\Resource\Page\PropertyValue\ButtonPropertyValue;
use PHPUnit\Framework\TestCase;

use function file_get_contents;
use function json_decode;

class ButtonPropertyValueTest extends TestCase
{
    public function testButtonPropertyHydratesAsReadOnly(): void
    {
        $rawData = $this->getPageRawData();
        $rawData['properties']['Action'] = [
            'id' => 'button-id',
            'type' => 'button',
            'button' => [],
        ];

        /** @var Page $page */
        $page = Page::fromRawData($rawData);

        $button = $page->getProperties()['Action'];

        $this->assertInstanceOf(ButtonPropertyValue::class, $button);
        $this->assertSame('button', $button->getType());
        $this->assertSame([], $button->getRawData()['button']);
        $this->assertArrayNotHasKey('Action', $page->toArrayForUpdate()['properties']);
    }

    private function getPageRawData(): array
    {
        return (array) json_decode(
            (string) file_get_contents('tests/Fixtures/client_pages_retrieve_page_200.json'),
            true,
        );
    }
}

<?php

declare(strict_types=1);

namespace Brd6\Test\NotionSdkPhp\Resource\Page\PropertyValue;

use Brd6\NotionSdkPhp\Resource\Page;
use Brd6\NotionSdkPhp\Resource\Page\PropertyValue\UniqueIdPropertyValue;
use PHPUnit\Framework\TestCase;

use function file_get_contents;
use function json_decode;

class UniqueIdPropertyValueTest extends TestCase
{
    public function testUniqueIdProperty(): void
    {
        /** @var Page $page */
        $page = Page::fromRawData(
            (array) json_decode(
                (string) file_get_contents('tests/Fixtures/client_pages_retrieve_page_unique_id_200.json'),
                true,
            ),
        );

        $properties = $page->getProperties();
        /** @var UniqueIdPropertyValue $uniqueId */
        $uniqueId = $properties['test-ID'];

        $this->assertEquals('unique_id', $uniqueId->getType());
        $this->assertEquals(3, $uniqueId->getNumber());
        $this->assertEquals('RL', $uniqueId->getPrefix());
    }
}

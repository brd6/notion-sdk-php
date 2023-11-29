<?php

declare(strict_types=1);

namespace Brd6\Test\NotionSdkPhp\Resource;

use Brd6\NotionSdkPhp\Exception\InvalidResourceException;
use Brd6\NotionSdkPhp\Resource\File\Emoji;
use Brd6\NotionSdkPhp\Resource\File\External;
use Brd6\NotionSdkPhp\Resource\Page;
use Brd6\NotionSdkPhp\Resource\Page\PropertyValue\NumberPropertyValue;
use PHPUnit\Framework\TestCase;

use function count;
use function file_get_contents;
use function json_decode;

class PageTest extends TestCase
{
    public function testInvalidPage(): void
    {
        $this->expectException(InvalidResourceException::class);

        Page::fromRawData([]);
    }

    public function testPage(): void
    {
        /** @var Page $page */
        $page = Page::fromRawData(
            (array) json_decode(
                (string) file_get_contents('tests/Fixtures/client_pages_retrieve_page_200.json'),
                true,
            ),
        );

        $this->assertNotEmpty($page->getId());
        $this->assertNotEmpty($page->toArray());
    }

    public function testPageWithPageObject(): void
    {
        /** @var Page $page */
        $page = Page::fromRawData(
            (array) json_decode(
                (string) file_get_contents('tests/Fixtures/client_request_retrieve_page_200.json'),
                true,
            ),
        );

        $this->assertNotEmpty($page->getId());
        $this->assertNotEmpty($page->toArray());

        $icon = $page->getIcon();

        $this->assertNotEmpty($icon);
        $this->assertInstanceOf(Emoji::class, $icon);
        $this->assertEquals('emoji', $icon->getType());
        $this->assertNotEmpty($icon->getEmoji());

        $cover = $page->getCover();

        $this->assertNotEmpty($cover);
        $this->assertInstanceOf(External::class, $cover);
        $this->assertEquals('external', $cover->getType());

        $external = $cover->getExternal();

        $this->assertNotNull($external);
        $this->assertNotEmpty($external->getUrl());
    }

    public function testPageProperties(): void
    {
        /** @var Page $page */
        $page = Page::fromRawData(
            (array) json_decode(
                (string) file_get_contents('tests/Fixtures/client_pages_retrieve_page_properties_200.json'),
                true,
            ),
        );

        $properties = $page->getProperties();

        $this->assertGreaterThan(0, count($page->getProperties()));

        foreach ($properties as $property) {
            $this->assertNotEmpty($property->getType());
            $this->assertNotEmpty($property->toArray());
        }
    }

    public function testNumberProperty(): void
    {
        /** @var Page $page */
        $page = Page::fromRawData(
            (array) json_decode(
                (string) file_get_contents('tests/Fixtures/client_pages_retrieve_page_properties_200.json'),
                true,
            ),
        );

        $properties = $page->getProperties();

        /** @var NumberPropertyValue $myNumber */
        $myNumber = $properties['My number'];

        $this->assertInstanceOf(NumberPropertyValue::class, $myNumber);
        $this->assertEquals(42, $myNumber->getNumber());

        $myNumberFloat = $properties['My number float'];

        $this->assertInstanceOf(NumberPropertyValue::class, $myNumberFloat);
        $this->assertEquals(42.42, $myNumberFloat->getNumber());
    }
}

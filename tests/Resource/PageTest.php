<?php

declare(strict_types=1);

namespace Brd6\Test\NotionSdkPhp\Resource;

use Brd6\NotionSdkPhp\Exception\InvalidResourceException;
use Brd6\NotionSdkPhp\Resource\File\Emoji;
use Brd6\NotionSdkPhp\Resource\File\External;
use Brd6\NotionSdkPhp\Resource\File\Icon;
use Brd6\NotionSdkPhp\Resource\Page;
use Brd6\NotionSdkPhp\Resource\Page\Parent\DatabaseIdParent;
use Brd6\NotionSdkPhp\Resource\Page\PropertyValue\NumberPropertyValue;
use Brd6\NotionSdkPhp\Resource\Page\PropertyValue\TitlePropertyValue;
use Brd6\NotionSdkPhp\Resource\RichText\Text;
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

    public function testPageWithIconObject(): void
    {
        /** @var array $rawData */
        $rawData = (array) json_decode(
            (string) file_get_contents('tests/Fixtures/client_request_retrieve_page_200.json'),
            true,
        );

        $rawData['icon'] = [
            'type' => 'icon',
            'icon' => [
                'name' => 'book',
                'color' => 'gray',
            ],
        ];

        /** @var Page $page */
        $page = Page::fromRawData($rawData);

        $icon = $page->getIcon();
        $this->assertNotNull($icon);
        $this->assertInstanceOf(Icon::class, $icon);
        $this->assertSame('icon', $icon->getType());
        $this->assertNotNull($icon->getIcon());
        $this->assertSame('book', $icon->getIcon()->getName());
        $this->assertSame('gray', $icon->getIcon()->getColor());
    }

    public function testPageIsArchivedReturnsFalseWhenUnset(): void
    {
        $page = new Page();

        $this->assertFalse($page->isArchived());
    }

    public function testPageToArrayForCreateDoesNotIncludeArchivedWhenUnset(): void
    {
        $page = $this->createPageForCreateSerialization();

        $data = $page->toArrayForCreate();
        $this->assertArrayNotHasKey('archived', $data);
    }

    /**
     * @dataProvider archivedValuesProvider
     */
    public function testPageToArrayForCreateIncludesArchivedWhenExplicitlySet(bool $archived): void
    {
        $page = $this->createPageForCreateSerialization()
            ->setArchived($archived);

        $data = $page->toArrayForCreate();
        $this->assertArrayHasKey('archived', $data);
        $this->assertSame($archived, $data['archived']);
    }

    public function testPageToArrayForUpdateDoesNotIncludeArchivedWhenUnset(): void
    {
        $page = $this->createPageForUpdateSerialization();

        $data = $page->toArrayForUpdate();
        $this->assertArrayNotHasKey('archived', $data);
    }

    /**
     * @dataProvider archivedValuesProvider
     */
    public function testPageToArrayForUpdateIncludesArchivedWhenExplicitlySet(bool $archived): void
    {
        $page = $this->createPageForUpdateSerialization()
            ->setArchived($archived);

        $data = $page->toArrayForUpdate();
        $this->assertArrayHasKey('archived', $data);
        $this->assertSame($archived, $data['archived']);
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

    /**
     * @return bool[][]
     */
    public static function archivedValuesProvider(): array
    {
        return [
            [false],
            [true],
        ];
    }

    private function createPageForCreateSerialization(): Page
    {
        return (new Page())
            ->setParent((new DatabaseIdParent())->setDatabaseId('248104cd-477e-80fd-b757-e945d38000bd'))
            ->setProperties([
                'title' => (new TitlePropertyValue())->setTitle([Text::fromContent('Test')]),
            ]);
    }

    private function createPageForUpdateSerialization(): Page
    {
        return (new Page())
            ->setProperties([
                'title' => (new TitlePropertyValue())->setTitle([Text::fromContent('Test')]),
            ]);
    }
}

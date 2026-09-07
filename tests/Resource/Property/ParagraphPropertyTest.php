<?php

declare(strict_types=1);

namespace Brd6\Test\NotionSdkPhp\Resource\Property;

use Brd6\NotionSdkPhp\Exception\InvalidFileException;
use Brd6\NotionSdkPhp\Exception\UnsupportedFileTypeException;
use Brd6\NotionSdkPhp\Resource\Block\AbstractBlock;
use Brd6\NotionSdkPhp\Resource\Block\ParagraphBlock;
use Brd6\NotionSdkPhp\Resource\File\AbstractFile;
use Brd6\NotionSdkPhp\Resource\Property\HeadingProperty;
use Brd6\NotionSdkPhp\Resource\Property\ParagraphProperty;
use PHPUnit\Framework\TestCase;

use function file_get_contents;
use function json_decode;

class ParagraphPropertyTest extends TestCase
{
    public function testParagraphBlockPreservesTabLabelIcon(): void
    {
        $icon = ['type' => 'emoji', 'emoji' => '📅'];
        $rawData = (array) json_decode(
            (string) file_get_contents('tests/Fixtures/client_blocks_retrieve_block_paragraph_mention_200.json'),
            true,
        );
        $rawData['paragraph']['icon'] = $icon;
        $block = AbstractBlock::fromRawData($rawData);

        $serialized = $block->toArray()['paragraph']['icon'] ?? [];
        $this->assertSame($icon['type'], $serialized['type'] ?? null);
        $this->assertSame($icon['emoji'], $serialized['emoji'] ?? null);
    }

    /** @dataProvider iconProvider */
    public function testIconRoundTripUsesExistingFileTypes(array $icon): void
    {
        $property = ParagraphProperty::fromRawData(['icon' => $icon]);

        $this->assertInstanceOf(AbstractFile::class, $property->getIcon());
        $expected = AbstractFile::fromRawData($icon)->toArray();
        $this->assertSame($expected, $property->toArray()['icon']);
        $this->assertSame($expected, $property->getIcon()->toArray());
    }

    public function iconProvider(): array
    {
        return [
            'emoji' => [['type' => 'emoji', 'emoji' => '📅']],
            'native' => [['type' => 'icon', 'icon' => ['name' => 'calendar', 'color' => 'blue']]],
            'external' => [['type' => 'external', 'external' => ['url' => 'https://example.com/icon.png']]],
            'file' => [['type' => 'file', 'file' => ['url' => 'https://example.com/icon.png', 'expiry_time' => '2026-09-07T16:00:00.000Z']]],
            'custom emoji' => [[
                'type' => 'custom_emoji',
                'custom_emoji' => ['id' => 'emoji-id', 'name' => 'calendar', 'url' => 'https://example.com/icon.png'],
            ],
            ],
        ];
    }

    public function testMissingAndNullIconsPreserveDefaultSerialization(): void
    {
        $withoutIcon = ParagraphProperty::fromRawData([]);
        $nullIcon = ParagraphProperty::fromRawData(['icon' => null]);

        $this->assertNull($withoutIcon->getIcon());
        $this->assertNull($nullIcon->getIcon());
        $this->assertSame([], $withoutIcon->toArray());
        $this->assertSame([], $nullIcon->toArray());
        $this->assertArrayHasKey('icon', $nullIcon->toArray(false));
        $this->assertNull($nullIcon->toArray(false)['icon']);
    }

    public function testIconCanBeSetAndClearedWithoutChangingTextOrChildren(): void
    {
        $rawData = (array) json_decode(
            (string) file_get_contents('tests/Fixtures/client_blocks_retrieve_block_paragraph_mention_200.json'),
            true,
        );
        $body = $rawData['paragraph'];
        $body['children'] = [$rawData];
        $property = ParagraphProperty::fromRawData($body);
        $before = $property->toArray();
        $icon = AbstractFile::fromRawData(['type' => 'emoji', 'emoji' => '📅']);

        $this->assertSame($property, $property->setIcon($icon));
        $this->assertSame($before['rich_text'], $property->toArray()['rich_text']);
        $this->assertSame($before['children'], $property->toArray()['children']);
        $block = (new ParagraphBlock())->setParagraph($property);
        $this->assertSame($icon->toArray(), $block->toArrayForCreate()['paragraph']['icon']);
        $this->assertSame($property, $property->setIcon(null));
        $this->assertSame($before, $property->toArray());
    }

    public function testMalformedIconUsesExistingParserError(): void
    {
        $this->expectException(InvalidFileException::class);

        ParagraphProperty::fromRawData(['icon' => []]);
    }

    public function testUnknownIconTypeUsesExistingParserError(): void
    {
        $this->expectException(UnsupportedFileTypeException::class);

        ParagraphProperty::fromRawData(['icon' => ['type' => 'unknown']]);
    }

    public function testOtherParagraphLikeTypesDoNotGainIconFields(): void
    {
        $heading = HeadingProperty::fromRawData([]);

        $this->assertArrayNotHasKey('icon', $heading->toArray(false));
    }
}

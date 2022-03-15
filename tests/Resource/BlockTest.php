<?php

declare(strict_types=1);

namespace Brd6\Test\NotionSdkPhp\Resource;

use Brd6\NotionSdkPhp\Exception\InvalidResourceException;
use Brd6\NotionSdkPhp\Resource\AbstractBlock;
use Brd6\NotionSdkPhp\Resource\Block\ParagraphBlock;
use Brd6\NotionSdkPhp\Resource\Property\HeadingProperty;
use Brd6\NotionSdkPhp\Resource\Property\ParagraphProperty;
use Brd6\NotionSdkPhp\Resource\RichText\TextRichText;
use Brd6\NotionSdkPhp\Util\StringHelper;
use PHPUnit\Framework\TestCase;

use function count;
use function file_get_contents;
use function json_decode;
use function str_replace;

class BlockTest extends TestCase
{
    public function testInvalidBlock(): void
    {
        $this->expectException(InvalidResourceException::class);

        AbstractBlock::fromRawData([]);
    }

    public function testInvalidBlockType(): void
    {
        $this->expectException(InvalidResourceException::class);

        AbstractBlock::fromRawData([
            'type' => 'invalid_type',
        ]);
    }

    public function testBlock(): void
    {
        $block = AbstractBlock::fromRawData(
            (array) json_decode(
                (string) file_get_contents('tests/fixtures/client_blocks_retrieve_block_child_page_200.json'),
                true,
            ),
        );

        $this->assertNotEmpty($block->getType());
        $this->assertNotEmpty($block->getId());
    }

    public function testParagraphBlock(): void
    {
        $block = AbstractBlock::fromRawData(
            (array) json_decode(
                (string) file_get_contents('tests/fixtures/client_blocks_retrieve_block_200.json'),
                true,
            ),
        );

        $this->assertInstanceOf(ParagraphBlock::class, $block);
        $this->assertEquals('paragraph', $block->getType());
        $this->assertNotNull($block->getParagraph());
        $this->assertInstanceOf(ParagraphProperty::class, $block->getParagraph());
        $this->assertGreaterThan(0, count($block->getParagraph()->getRichTexts()));

        $richText = $block->getParagraph()->getRichTexts()[0];

        $this->assertInstanceOf(TextRichText::class, $richText);
        $this->assertEquals('text', $richText->getType());

        $this->assertNotEmpty($richText->getAnnotations());
        $this->assertNotEmpty($richText->getContent());
    }

    public function testHeadingsBlock(): void
    {
        $headings = [
            'heading_1',
            'heading_2',
            'heading_3',
        ];

        foreach ($headings as $heading) {
            $rawContent = (string) file_get_contents('tests/fixtures/client_blocks_retrieve_block_heading1_200.json');

            $rawContent = str_replace('heading_1', $heading, $rawContent);

            $block = AbstractBlock::fromRawData(
                (array) json_decode(
                    $rawContent,
                    true,
                ),
            );

            $typeFormatted = StringHelper::snakeCaseToCamelCase($block->getType());
            $getterMethodName = "get$typeFormatted";

            $this->assertEquals($heading, $block->getType());
            $this->assertNotNull($block->$getterMethodName());
            $this->assertInstanceOf(HeadingProperty::class, $block->$getterMethodName());
            $this->assertGreaterThan(0, count($block->$getterMethodName()->getRichTexts()));

            $richText = $block->$getterMethodName()->getRichTexts()[0];

            $this->assertInstanceOf(TextRichText::class, $richText);
            $this->assertEquals('text', $richText->getType());

            $this->assertNotEmpty($richText->getAnnotations());
            $this->assertNotEmpty($richText->getContent());
        }
    }
}

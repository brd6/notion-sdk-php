<?php

declare(strict_types=1);

namespace Brd6\Test\NotionSdkPhp\Resource;

use Brd6\NotionSdkPhp\Exception\InvalidResourceException;
use Brd6\NotionSdkPhp\Resource\AbstractBlock;
use Brd6\NotionSdkPhp\Resource\AbstractFile;
use Brd6\NotionSdkPhp\Resource\Block\CalloutBlock;
use Brd6\NotionSdkPhp\Resource\Block\ChildPageBlock;
use Brd6\NotionSdkPhp\Resource\Block\ParagraphBlock;
use Brd6\NotionSdkPhp\Resource\File\Emoji;
use Brd6\NotionSdkPhp\Resource\Property\CalloutProperty;
use Brd6\NotionSdkPhp\Resource\Property\ChildPageProperty;
use Brd6\NotionSdkPhp\Resource\Property\HeadingProperty;
use Brd6\NotionSdkPhp\Resource\Property\ParagraphProperty;
use Brd6\NotionSdkPhp\Resource\RichText\Text;
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

    public function testChildPageBlock(): void
    {
        $block = AbstractBlock::fromRawData(
            (array) json_decode(
                (string) file_get_contents('tests/fixtures/client_blocks_retrieve_block_child_page_200.json'),
                true,
            ),
        );

        $this->assertInstanceOf(ChildPageBlock::class, $block);
        $this->assertEquals('child_page', $block->getType());
        $this->assertNotNull($block->getChildPage());
        $this->assertInstanceOf(ChildPageProperty::class, $block->getChildPage());
        $this->assertNotEmpty($block->getChildPage()->getTitle());
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
        $this->assertGreaterThan(0, count($block->getParagraph()->getRichText()));

        $richText = $block->getParagraph()->getRichText()[0];

        $this->assertInstanceOf(Text::class, $richText);
        $this->assertEquals('text', $richText->getType());

        $this->assertNotEmpty($richText->getAnnotations());
        $this->assertNotNull($richText->getText());
        $this->assertNotEmpty($richText->getText()->getContent());
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
            $this->assertGreaterThan(0, count($block->$getterMethodName()->getRichText()));

            $richText = $block->$getterMethodName()->getRichText()[0];

            $this->assertInstanceOf(Text::class, $richText);
            $this->assertEquals('text', $richText->getType());

            $this->assertNotEmpty($richText->getAnnotations());
            $this->assertNotNull($richText->getText());
            $this->assertNotEmpty($richText->getText()->getContent());
        }
    }

    public function testCalloutBlock(): void
    {
        $block = AbstractBlock::fromRawData(
            (array) json_decode(
                (string) file_get_contents('tests/fixtures/client_blocks_retrieve_block_callout_200.json'),
                true,
            ),
        );

        $this->assertInstanceOf(CalloutBlock::class, $block);
        $this->assertEquals('callout', $block->getType());
        $this->assertNotNull($block->getCallout());
        $this->assertInstanceOf(CalloutProperty::class, $block->getCallout());
        $this->assertGreaterThan(0, count($block->getCallout()->getRichText()));

        $this->assertNotNull($block->getCallout()->getIcon());
        $this->assertInstanceOf(AbstractFile::class, $block->getCallout()->getIcon());

        $this->assertEquals('emoji', $block->getCallout()->getIcon()->getType());
        $this->assertInstanceOf(Emoji::class, $block->getCallout()->getIcon());
        $this->assertNotEmpty($block->getCallout()->getIcon()->getEmoji());

        $richText = $block->getCallout()->getRichText()[0];

        $this->assertInstanceOf(Text::class, $richText);
        $this->assertEquals('text', $richText->getType());

        $this->assertNotEmpty($richText->getAnnotations());
        $this->assertNotNull($richText->getText());
        $this->assertNotEmpty($richText->getText()->getContent());

        $this->assertNotEmpty($block->getCallout()->getColor());
    }
}

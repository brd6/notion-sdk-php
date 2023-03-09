<?php

declare(strict_types=1);

namespace Brd6\Test\NotionSdkPhp\Resource;

use Brd6\NotionSdkPhp\Exception\InvalidResourceException;
use Brd6\NotionSdkPhp\Resource\Block\AbstractBlock;
use Brd6\NotionSdkPhp\Resource\Block\CalloutBlock;
use Brd6\NotionSdkPhp\Resource\Block\ChildPageBlock;
use Brd6\NotionSdkPhp\Resource\Block\ParagraphBlock;
use Brd6\NotionSdkPhp\Resource\Block\SyncedBlockBlock;
use Brd6\NotionSdkPhp\Resource\File\AbstractFile;
use Brd6\NotionSdkPhp\Resource\File\Emoji;
use Brd6\NotionSdkPhp\Resource\Property\CalloutProperty;
use Brd6\NotionSdkPhp\Resource\Property\ChildPageProperty;
use Brd6\NotionSdkPhp\Resource\Property\HeadingProperty;
use Brd6\NotionSdkPhp\Resource\Property\ParagraphProperty;
use Brd6\NotionSdkPhp\Resource\Property\SyncedBlockProperty;
use Brd6\NotionSdkPhp\Resource\RichText\Equation;
use Brd6\NotionSdkPhp\Resource\RichText\Mention;
use Brd6\NotionSdkPhp\Resource\RichText\MentionInterface;
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
                (string) file_get_contents('tests/Fixtures/client_blocks_retrieve_block_child_page_200.json'),
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
                (string) file_get_contents('tests/Fixtures/client_blocks_retrieve_block_child_page_200.json'),
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
                (string) file_get_contents('tests/Fixtures/client_blocks_retrieve_block_200.json'),
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
        $this->assertNotEmpty($richText->getPlainText());
    }

    public function testHeadingsBlock(): void
    {
        $headings = [
            'heading_1',
            'heading_2',
            'heading_3',
        ];

        foreach ($headings as $heading) {
            $rawContent = (string) file_get_contents('tests/Fixtures/client_blocks_retrieve_block_heading1_200.json');

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
                (string) file_get_contents('tests/Fixtures/client_blocks_retrieve_block_callout_200.json'),
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

    public function testMentionsBlock(): void
    {
        $block = AbstractBlock::fromRawData(
            (array) json_decode(
                (string) file_get_contents('tests/Fixtures/client_blocks_retrieve_block_paragraph_mention_200.json'),
                true,
            ),
        );

        $this->assertInstanceOf(ParagraphBlock::class, $block);
        $this->assertNotNull($block->getParagraph());
        $this->assertInstanceOf(ParagraphProperty::class, $block->getParagraph());
        $this->assertGreaterThan(0, count($block->getParagraph()->getRichText()));

        foreach ($block->getParagraph()->getRichText() as $richText) {
            if ($richText->getType() !== 'mention') {
                continue;
            }

            $this->assertInstanceOf(Mention::class, $richText);
            $this->assertInstanceOf(MentionInterface::class, $richText->getMention());

            $this->assertNotEmpty($richText->getMention()->getType());
        }
    }

    public function testEquationRichText(): void
    {
        $block = AbstractBlock::fromRawData(
            (array) json_decode(
                (string) file_get_contents('tests/Fixtures/client_blocks_retrieve_block_paragraph_equation_200.json'),
                true,
            ),
        );

        $this->assertInstanceOf(ParagraphBlock::class, $block);
        $this->assertEquals('paragraph', $block->getType());
        $this->assertNotNull($block->getParagraph());
        $this->assertInstanceOf(ParagraphProperty::class, $block->getParagraph());
        $this->assertGreaterThan(0, count($block->getParagraph()->getRichText()));

        $richText = $block->getParagraph()->getRichText()[0];

        $this->assertEquals('equation', $richText->getType());
        $this->assertInstanceOf(Equation::class, $richText);

        $this->assertNotNull($richText->getEquation());
        $this->assertNotEmpty($richText->getEquation()->getExpression());
    }

    public function testSyncedBlock(): void
    {
        $block = AbstractBlock::fromRawData(
            (array) json_decode(
                (string) file_get_contents('tests/Fixtures/client_blocks_retrieve_block_synced_block_200.json'),
                true,
            ),
        );

        $this->assertInstanceOf(SyncedBlockBlock::class, $block);
        $this->assertNotNull($block->getSyncedBlock());
        $this->assertInstanceOf(SyncedBlockProperty::class, $block->getSyncedBlock());
        $this->assertEquals(
            '154ee1b6-887d-4408-b198-e88337a9aefe',
            $block->getSyncedBlock()->getSyncedFrom()->getBlockId(),
        );
    }

    public function testFromRawDataWithEmptyContentInRichText(): void
    {
        /** @var ParagraphBlock $block */
        $block = AbstractBlock::fromRawData(
            (array) json_decode(
                (string) file_get_contents('tests/Fixtures/client_blocks_retrieve_block_empty_text_content_200.json'),
                true,
            ),
        );

        /** @var Text $richText */
        $richText = $block->getParagraph()->getRichText()[0];

        $this->assertEquals('', $richText->getText()->getContent());
        $this->assertEquals('', $richText->getPlainText());
    }

    public function testCreateEmptyContentInRichText(): void
    {
        $richText = Text::fromContent('');

        $this->assertEquals('', $richText->getText()->getContent());
        $this->assertEquals('', $richText->getPlainText());

        $richTextData = $richText->toArray();

        $this->assertArrayHasKey('text', $richTextData);
        $this->assertArrayHasKey('content', $richTextData['text']);
        $this->assertEquals('', $richTextData['text']['content']);
    }
}

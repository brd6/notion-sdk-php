<?php

declare(strict_types=1);

namespace Brd6\Test\NotionSdkPhp\Resource;

use Brd6\NotionSdkPhp\Exception\InvalidResourceException;
use Brd6\NotionSdkPhp\Resource\Block\AbstractBlock;
use Brd6\NotionSdkPhp\Resource\Block\AudioBlock;
use Brd6\NotionSdkPhp\Resource\Block\CalloutBlock;
use Brd6\NotionSdkPhp\Resource\Block\ChildPageBlock;
use Brd6\NotionSdkPhp\Resource\Block\ColumnBlock;
use Brd6\NotionSdkPhp\Resource\Block\ColumnListBlock;
use Brd6\NotionSdkPhp\Resource\Block\Heading1Block;
use Brd6\NotionSdkPhp\Resource\Block\Heading2Block;
use Brd6\NotionSdkPhp\Resource\Block\Heading3Block;
use Brd6\NotionSdkPhp\Resource\Block\Heading4Block;
use Brd6\NotionSdkPhp\Resource\Block\Heading5Block;
use Brd6\NotionSdkPhp\Resource\Block\Heading6Block;
use Brd6\NotionSdkPhp\Resource\Block\MeetingNotesBlock;
use Brd6\NotionSdkPhp\Resource\Block\ParagraphBlock;
use Brd6\NotionSdkPhp\Resource\Block\SyncedBlockBlock;
use Brd6\NotionSdkPhp\Resource\Block\TabBlock;
use Brd6\NotionSdkPhp\Resource\Block\TranscriptionBlock;
use Brd6\NotionSdkPhp\Resource\Block\UnsupportedBlock;
use Brd6\NotionSdkPhp\Resource\File\AbstractFile;
use Brd6\NotionSdkPhp\Resource\File\Emoji;
use Brd6\NotionSdkPhp\Resource\Property\CalloutProperty;
use Brd6\NotionSdkPhp\Resource\Property\ChildPageProperty;
use Brd6\NotionSdkPhp\Resource\Property\FileProperty;
use Brd6\NotionSdkPhp\Resource\Property\HeadingProperty;
use Brd6\NotionSdkPhp\Resource\Property\MeetingNotesProperty;
use Brd6\NotionSdkPhp\Resource\Property\ParagraphProperty;
use Brd6\NotionSdkPhp\Resource\Property\SyncedBlockProperty;
use Brd6\NotionSdkPhp\Resource\Property\TabProperty;
use Brd6\NotionSdkPhp\Resource\RichText\Equation;
use Brd6\NotionSdkPhp\Resource\RichText\Mention;
use Brd6\NotionSdkPhp\Resource\RichText\Mention\CustomEmojiMention;
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

    public function testUnsupportedBlock(): void
    {
        $block = AbstractBlock::fromRawData([
            'object' => 'block',
            'type' => 'unsupported_block',
        ]);

        $this->assertInstanceOf(UnsupportedBlock::class, $block);
        $this->assertEquals('unsupported_block', $block->getType());
    }

    public function testUnsupportedBlockExposesBlockType(): void
    {
        /** @var UnsupportedBlock $block */
        $block = AbstractBlock::fromRawData([
            'object' => 'block',
            'id' => 'c02fc1d3-db8b-45c5-a222-27595b15aea7',
            'type' => 'unsupported',
            'unsupported' => [
                'block_type' => 'form',
            ],
        ]);

        $this->assertInstanceOf(UnsupportedBlock::class, $block);
        $this->assertEquals('unsupported', $block->getType());
        $this->assertEquals('form', $block->getBlockType());
    }

    public function testUnknownBlockTypeHasNoBlockType(): void
    {
        /** @var UnsupportedBlock $block */
        $block = AbstractBlock::fromRawData([
            'object' => 'block',
            'type' => 'unsupported_block',
        ]);

        $this->assertNull($block->getBlockType());
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
            'heading_1' => Heading1Block::class,
            'heading_2' => Heading2Block::class,
            'heading_3' => Heading3Block::class,
            'heading_4' => Heading4Block::class,
            'heading_5' => Heading5Block::class,
            'heading_6' => Heading6Block::class,
        ];

        foreach ($headings as $heading => $blockClass) {
            $rawContent = (string) file_get_contents('tests/Fixtures/client_blocks_retrieve_block_heading1_200.json');

            $rawContent = str_replace('heading_1', $heading, $rawContent);
            $rawData = (array) json_decode($rawContent, true);

            $block = AbstractBlock::fromRawData(
                $rawData,
            );

            $blockData = $block->toArray();
            $typeFormatted = StringHelper::snakeCaseToCamelCase($block->getType());
            $getterMethodName = "get$typeFormatted";

            $this->assertInstanceOf($blockClass, $block);
            $this->assertEquals($heading, $block->getType());
            $this->assertNotNull($block->$getterMethodName());
            $this->assertInstanceOf(HeadingProperty::class, $block->$getterMethodName());
            $this->assertGreaterThan(0, count($block->$getterMethodName()->getRichText()));
            $this->assertArrayHasKey($heading, $blockData);
            $this->assertSame($rawData[$heading]['color'], $blockData[$heading]['color']);
            $this->assertSame($rawData[$heading]['rich_text'][0]['type'], $blockData[$heading]['rich_text'][0]['type']);
            $this->assertEquals($rawData[$heading]['rich_text'][0]['annotations'], $blockData[$heading]['rich_text'][0]['annotations']);
            $this->assertSame($rawData[$heading]['rich_text'][0]['plain_text'], $blockData[$heading]['rich_text'][0]['plain_text']);
            $this->assertSame(
                $rawData[$heading]['rich_text'][0]['text']['content'],
                $blockData[$heading]['rich_text'][0]['text']['content'],
            );

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

    public function testSyncedBlockWithoutBlockId(): void
    {
        $block = AbstractBlock::fromRawData(
            (array) json_decode(
                (string) file_get_contents('tests/Fixtures/client_blocks_retrieve_block_synced_block_no_block_id_200.json'),
                true,
            ),
        );

        $this->assertInstanceOf(SyncedBlockBlock::class, $block);
        $this->assertNotNull($block->getSyncedBlock());
        $this->assertInstanceOf(SyncedBlockProperty::class, $block->getSyncedBlock());
        $this->assertNotNull($block->getSyncedBlock()->getSyncedFrom());
        $this->assertEquals('block_id', $block->getSyncedBlock()->getSyncedFrom()->getType());
        $this->assertSame('', $block->getSyncedBlock()->getSyncedFrom()->getBlockId());
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

    public function testAudioBlock(): void
    {
        $block = AbstractBlock::fromRawData(
            (array) json_decode(
                (string) file_get_contents('tests/Fixtures/client_blocks_retrieve_block_audio_200.json'),
                true,
            ),
        );

        $this->assertInstanceOf(AudioBlock::class, $block);
        $this->assertEquals('audio', $block->getType());
        $this->assertNotNull($block->getAudio());
        $this->assertInstanceOf(AbstractFile::class, $block->getAudio());

        $audioFile = $block->getAudio()->getFile();

        $this->assertNotNull($audioFile);
        $this->assertInstanceOf(FileProperty::class, $audioFile);
        $this->assertNotEmpty($audioFile->getUrl());
        $this->assertNotEmpty($audioFile->getExpiryTime());
    }

    public function testCustomEmojiBlock(): void
    {
        $block = AbstractBlock::fromRawData(
            (array) json_decode(
                (string) file_get_contents('tests/Fixtures/client_blocks_retrieve_block_custom_emoji_200.json'),
                true,
            ),
        );

        $this->assertInstanceOf(CalloutBlock::class, $block);
        $this->assertEquals('callout', $block->getType());
        $this->assertNotNull($block->getCallout());
        $this->assertInstanceOf(CalloutProperty::class, $block->getCallout());

        $icon = $block->getCallout()->getIcon();
        $this->assertNotNull($icon);
        $this->assertEquals('custom_emoji', $icon->getType());

        $customEmoji = $icon->getCustomEmoji();
        $this->assertNotNull($customEmoji);
        $this->assertEquals('45ce454c-d427-4f53-9489-e5d0f3d1db6b', $customEmoji->getId());
        $this->assertEquals('bufo', $customEmoji->getName());
        $this->assertEquals(
            'https://s3-us-west-2.amazonaws.com/public.notion-static.com/'
            . '865e85fc-7442-44d3-b323-9b03a2111720/3c6796979c50f4aa.png',
            $customEmoji->getUrl(),
        );
    }

    public function testCustomEmojiMentionBlock(): void
    {
        $block = AbstractBlock::fromRawData(
            (array) json_decode(
                (string) file_get_contents('tests/Fixtures/client_blocks_retrieve_block_paragraph_custom_emoji_mention_200.json'),
                true,
            ),
        );

        $this->assertInstanceOf(ParagraphBlock::class, $block);
        $this->assertNotNull($block->getParagraph());
        $this->assertInstanceOf(ParagraphProperty::class, $block->getParagraph());
        $this->assertCount(1, $block->getParagraph()->getRichText());

        $richText = $block->getParagraph()->getRichText()[0];
        $this->assertInstanceOf(Mention::class, $richText);
        $this->assertEquals('mention', $richText->getType());

        $mention = $richText->getMention();
        $this->assertInstanceOf(CustomEmojiMention::class, $mention);
        $this->assertEquals('custom_emoji', $mention->getType());

        $customEmoji = $mention->getCustomEmoji();
        $this->assertNotNull($customEmoji);
        $this->assertEquals('45ce454c-d427-4f53-9489-e5d0f3d1db6b', $customEmoji->getId());
        $this->assertEquals('bufo', $customEmoji->getName());
        $this->assertEquals(
            'https://s3-us-west-2.amazonaws.com/public.notion-static.com/'
            . '865e85fc-7442-44d3-b323-9b03a2111720/3c6796979c50f4aa.png',
            $customEmoji->getUrl(),
        );
    }

    public function testColumnListBlockWithChildren(): void
    {
        $fixture = (array) json_decode(
            (string) file_get_contents('tests/Fixtures/client_blocks_retrieve_column_list_200.json'),
            true,
        );

        /** @var ColumnListBlock $block */
        $block = AbstractBlock::fromRawData($fixture);

        $this->assertInstanceOf(ColumnListBlock::class, $block);
        $this->assertEquals('column_list', $block->getType());
        $this->assertTrue($block->isHasChildren());
        $this->assertNotEmpty($block->getChildren());
        $this->assertCount(1, $block->getChildren());

        /** @var ColumnBlock $columnBlock */
        $columnBlock = $block->getChildren()[0];
        $this->assertInstanceOf(ColumnBlock::class, $columnBlock);
        $this->assertTrue($columnBlock->isHasChildren());
        $this->assertNotEmpty($columnBlock->getChildren());
        $this->assertCount(1, $columnBlock->getChildren());

        /** @var ParagraphBlock $paragraphBlock */
        $paragraphBlock = $columnBlock->getChildren()[0];
        $this->assertInstanceOf(ParagraphBlock::class, $paragraphBlock);
        $this->assertFalse($paragraphBlock->isHasChildren());
        $this->assertEmpty($paragraphBlock->getChildren());
    }

    public function testBlockWithHasChildrenButNoChildrenInPayload(): void
    {
        $fixture = [
            'object' => 'block',
            'id' => 'a1b2c3d4-e5f6-a1b2-c3d4-e5f6a1b2c3d4',
            'has_children' => true,
            'type' => 'paragraph',
            'paragraph' => [
                'rich_text' => [],
            ],
            'created_time' => '2022-03-22T13:42:00.000Z',
            'last_edited_time' => '2022-03-22T14:02:00.000Z',
            'created_by' => [
                'object' => 'user',
                'id' => '7f03dda0-a132-49d7-b8b2-29c9ed1b1f0e',
            ],
            'last_edited_by' => [
                'object' => 'user',
                'id' => '7f03dda0-a132-49d7-b8b2-29c9ed1b1f0e',
            ],
            'archived' => false,
        ];

        /** @var AbstractBlock $block */
        $block = AbstractBlock::fromRawData($fixture);

        $this->assertInstanceOf(ParagraphBlock::class, $block);
        $this->assertTrue($block->isHasChildren());
        $this->assertEmpty($block->getChildren());
    }

    public function testBlockHydratesInTrashPayloadWithoutArchivedKey(): void
    {
        $rawData = (array) json_decode(
            (string) file_get_contents('tests/Fixtures/client_blocks_retrieve_block_200.json'),
            true,
        );
        unset($rawData['archived']);
        $rawData['in_trash'] = true;

        $block = AbstractBlock::fromRawData($rawData);

        $this->assertTrue($block->isArchived());
        $this->assertTrue($block->isInTrash());
    }

    public function testBlockHydratesArchivedPayload(): void
    {
        $rawData = (array) json_decode(
            (string) file_get_contents('tests/Fixtures/client_blocks_retrieve_block_200.json'),
            true,
        );
        $rawData['archived'] = true;
        unset($rawData['in_trash']);

        $block = AbstractBlock::fromRawData($rawData);

        $this->assertTrue($block->isArchived());
        $this->assertTrue($block->isInTrash());
    }

    public function testTabBlock(): void
    {
        $block = AbstractBlock::fromRawData(
            (array) json_decode(
                (string) file_get_contents('tests/Fixtures/client_blocks_retrieve_block_tab_200.json'),
                true,
            ),
        );

        $this->assertInstanceOf(TabBlock::class, $block);
        $this->assertEquals('tab', $block->getType());
        $this->assertInstanceOf(TabProperty::class, $block->getTab());
        $this->assertTrue($block->isHasChildren());
    }

    public function testTabBlockSerialization(): void
    {
        $block = new TabBlock();
        $block->setTab(new TabProperty());

        $this->assertEquals('tab', $block->toArrayForCreate()['type']);
    }

    public function testMeetingNotesBlock(): void
    {
        $block = AbstractBlock::fromRawData(
            (array) json_decode(
                (string) file_get_contents('tests/Fixtures/client_blocks_retrieve_block_meeting_notes_200.json'),
                true,
            ),
        );

        $this->assertInstanceOf(MeetingNotesBlock::class, $block);
        $this->assertEquals('meeting_notes', $block->getType());

        $property = $block->getMeetingNotes();
        $this->assertInstanceOf(MeetingNotesProperty::class, $property);
        $this->assertEquals('notes_ready', $property->getStatus());
        $this->assertInstanceOf(Text::class, $property->getTitle()[0]);
        $this->assertEquals('Weekly sync', $property->getTitle()[0]->getText()->getContent());
        $this->assertEquals('0d940186-ab70-4351-bb34-2d16f0635d50', $property->getChildren()['summary_block_id']);
        $this->assertNotEmpty($property->getCalendarEvent()['attendees']);
        $this->assertNotEmpty($property->getRecording()['start_time']);
    }

    public function testTranscriptionBlock(): void
    {
        $block = AbstractBlock::fromRawData(
            (array) json_decode(
                (string) file_get_contents('tests/Fixtures/client_blocks_retrieve_block_transcription_200.json'),
                true,
            ),
        );

        $this->assertInstanceOf(TranscriptionBlock::class, $block);
        $this->assertEquals('transcription', $block->getType());

        $property = $block->getTranscription();
        $this->assertInstanceOf(MeetingNotesProperty::class, $property);
        $this->assertEquals(MeetingNotesProperty::STATUS_TRANSCRIPTION_IN_PROGRESS, $property->getStatus());
        $this->assertEquals('Weekly sync', $property->getTitle()[0]->getText()->getContent());
    }
}

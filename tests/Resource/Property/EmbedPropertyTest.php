<?php

declare(strict_types=1);

namespace Brd6\Test\NotionSdkPhp\Resource\Property;

use Brd6\NotionSdkPhp\Resource\Block\EmbedBlock;
use Brd6\NotionSdkPhp\Resource\Property\EmbedProperty;
use Brd6\NotionSdkPhp\Resource\Property\FileUploadProperty;
use Brd6\NotionSdkPhp\Resource\RichText\Text;
use PHPUnit\Framework\TestCase;

use function array_column;

class EmbedPropertyTest extends TestCase
{
    public function testFromFileUpload(): void
    {
        $property = EmbedProperty::fromFileUpload('43833259-72ae-404e-8441-b6577f3159b4');

        $this->assertEquals(EmbedProperty::TYPE_FILE_UPLOAD, $property->getType());
        $this->assertInstanceOf(FileUploadProperty::class, $property->getFileUpload());
        $this->assertEquals('43833259-72ae-404e-8441-b6577f3159b4', $property->getFileUpload()->getId());
    }

    public function testFileUploadEmbedSerializesForCreate(): void
    {
        $block = new EmbedBlock();
        $block->setEmbed(EmbedProperty::fromFileUpload('43833259-72ae-404e-8441-b6577f3159b4'));

        $this->assertEquals([
            'object' => 'block',
            'type' => 'embed',
            'embed' => [
                'type' => 'file_upload',
                'file_upload' => [
                    'id' => '43833259-72ae-404e-8441-b6577f3159b4',
                ],
            ],
        ], $block->toArrayForCreate());
    }

    public function testUrlEmbedSerializationUnchanged(): void
    {
        $block = new EmbedBlock();
        $block->setEmbed((new EmbedProperty())->setUrl('https://example.com'));

        $this->assertEquals([
            'object' => 'block',
            'type' => 'embed',
            'embed' => [
                'url' => 'https://example.com',
            ],
        ], $block->toArrayForCreate());
    }

    public function testFromRawDataWithUrl(): void
    {
        $property = EmbedProperty::fromRawData(['url' => 'https://example.com']);

        $this->assertEquals('https://example.com', $property->getUrl());
        $this->assertNull($property->getType());
        $this->assertNull($property->getFileUpload());
    }

    public function testCaptionSurvivesSerialization(): void
    {
        $block = EmbedBlock::fromRawData([
            'object' => 'block',
            'id' => 'b8f0cb6d-1c3a-4c4e-9a52-6c8a0d1b7e11',
            'type' => 'embed',
            'created_time' => '2026-09-25T10:00:00.000Z',
            'created_by' => ['object' => 'user', 'id' => 'ee5f0f84-409a-440f-983a-a5315961c6e4'],
            'last_edited_time' => '2026-09-25T10:00:00.000Z',
            'last_edited_by' => ['object' => 'user', 'id' => 'ee5f0f84-409a-440f-983a-a5315961c6e4'],
            'has_children' => false,
            'embed' => [
                'url' => 'https://example.com',
                'caption' => [
                    [
                        'type' => 'text',
                        'text' => ['content' => 'Source: ', 'link' => null],
                        'annotations' => ['bold' => false, 'italic' => false, 'strikethrough' => false, 'underline' => false, 'code' => false, 'color' => 'default'],
                        'plain_text' => 'Source: ',
                        'href' => null,
                    ],
                    [
                        'type' => 'text',
                        'text' => ['content' => 'Example', 'link' => ['url' => 'https://example.com']],
                        'annotations' => ['bold' => true, 'italic' => false, 'strikethrough' => false, 'underline' => false, 'code' => false, 'color' => 'default'],
                        'plain_text' => 'Example',
                        'href' => 'https://example.com',
                    ],
                ],
            ],
        ]);

        $caption = $block->getEmbed()->getCaption();

        $this->assertCount(2, $caption);
        $this->assertInstanceOf(Text::class, $caption[1]);
        $this->assertEquals('Example', $caption[1]->getPlainText());
        foreach ([$block->toArray()['embed'], $block->toArrayForCreate()['embed']] as $data) {
            $this->assertEquals('https://example.com', $data['url']);
            $this->assertEquals(['Source: ', 'Example'], array_column(array_column($data['caption'], 'text'), 'content'));
            $this->assertEquals('https://example.com', $data['caption'][1]['text']['link']['url']);
        }
    }

    public function testFromRawDataWithoutUrl(): void
    {
        $property = EmbedProperty::fromRawData([
            'type' => 'file_upload',
            'file_upload' => ['id' => '43833259-72ae-404e-8441-b6577f3159b4'],
        ]);

        $this->assertEquals('', $property->getUrl());
        $this->assertEquals('file_upload', $property->getType());
        $this->assertEquals('43833259-72ae-404e-8441-b6577f3159b4', $property->getFileUpload()->getId());
    }
}

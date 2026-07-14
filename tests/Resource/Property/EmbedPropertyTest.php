<?php

declare(strict_types=1);

namespace Brd6\Test\NotionSdkPhp\Resource\Property;

use Brd6\NotionSdkPhp\Resource\Block\EmbedBlock;
use Brd6\NotionSdkPhp\Resource\Property\EmbedProperty;
use Brd6\NotionSdkPhp\Resource\Property\FileUploadProperty;
use PHPUnit\Framework\TestCase;

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

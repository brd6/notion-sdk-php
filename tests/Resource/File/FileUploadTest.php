<?php

declare(strict_types=1);

namespace Brd6\Test\NotionSdkPhp\Resource\File;

use Brd6\NotionSdkPhp\Exception\UnsupportedFileTypeException;
use Brd6\NotionSdkPhp\Resource\Block\AbstractBlock;
use Brd6\NotionSdkPhp\Resource\Block\ImageBlock;
use Brd6\NotionSdkPhp\Resource\File\AbstractFile;
use Brd6\NotionSdkPhp\Resource\File\FileUpload;
use Brd6\NotionSdkPhp\Resource\Property\FileUploadProperty;
use Brd6\Test\NotionSdkPhp\TestCase;

class FileUploadTest extends TestCase
{
    public function testUnsupportedFileTypeStillThrows(): void
    {
        $this->expectException(UnsupportedFileTypeException::class);

        AbstractFile::fromRawData([
            'type' => 'unknown_file_type',
            'unknown_file_type' => [],
        ]);
    }

    public function testFromRawData(): void
    {
        $file = AbstractFile::fromRawData([
            'type' => 'file_upload',
            'file_upload' => [
                'id' => 'b52b8ed6-e029-4707-a671-832549c09de3',
            ],
        ]);

        $this->assertInstanceOf(FileUpload::class, $file);
        $this->assertEquals('file_upload', $file->getType());
        $this->assertNotNull($file->getFileUpload());
        $this->assertEquals('b52b8ed6-e029-4707-a671-832549c09de3', $file->getFileUpload()->getId());
    }

    public function testSerialize(): void
    {
        $file = new FileUpload();
        $file->setFileUpload((new FileUploadProperty())->setId('b52b8ed6-e029-4707-a671-832549c09de3'));

        $this->assertEquals([
            'type' => 'file_upload',
            'file_upload' => [
                'id' => 'b52b8ed6-e029-4707-a671-832549c09de3',
            ],
        ], $file->toArray());
    }

    public function testImageBlockWithFileUploadHydrates(): void
    {
        $block = AbstractBlock::fromRawData([
            'object' => 'block',
            'id' => '0c940186-ab70-4351-bb34-2d16f0635d49',
            'created_time' => '2026-07-13T23:13:00.000Z',
            'last_edited_time' => '2026-07-13T23:13:00.000Z',
            'created_by' => [
                'object' => 'user',
                'id' => 'd0063369-46a4-4756-8798-0d36d53c9b20',
            ],
            'last_edited_by' => [
                'object' => 'user',
                'id' => 'd0063369-46a4-4756-8798-0d36d53c9b20',
            ],
            'has_children' => false,
            'archived' => false,
            'type' => 'image',
            'image' => [
                'type' => 'file_upload',
                'file_upload' => [
                    'id' => 'b52b8ed6-e029-4707-a671-832549c09de3',
                ],
            ],
        ]);

        $this->assertInstanceOf(ImageBlock::class, $block);

        $image = $block->getImage();
        $this->assertInstanceOf(FileUpload::class, $image);
        $this->assertEquals('b52b8ed6-e029-4707-a671-832549c09de3', $image->getFileUpload()->getId());
    }
}

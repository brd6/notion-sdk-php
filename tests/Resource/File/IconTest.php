<?php

declare(strict_types=1);

namespace Brd6\Test\NotionSdkPhp\Resource\File;

use Brd6\NotionSdkPhp\Resource\File\AbstractFile;
use Brd6\NotionSdkPhp\Resource\File\Icon;
use Brd6\Test\NotionSdkPhp\TestCase;

class IconTest extends TestCase
{
    public function testFromRawData(): void
    {
        $file = AbstractFile::fromRawData([
            'type' => 'icon',
            'icon' => [
                'name' => 'add',
                'color' => 'blue',
            ],
        ]);

        $this->assertInstanceOf(Icon::class, $file);
        $this->assertSame('icon', $file->getType());
        $this->assertNotNull($file->getIcon());
        $this->assertSame('add', $file->getIcon()->getName());
        $this->assertSame('blue', $file->getIcon()->getColor());
    }

    public function testToArrayRoundTrip(): void
    {
        $rawData = [
            'type' => 'icon',
            'icon' => [
                'name' => 'book',
                'color' => 'gray',
            ],
        ];

        $file = AbstractFile::fromRawData($rawData);

        $this->assertSame($rawData, $file->toArray());
    }
}

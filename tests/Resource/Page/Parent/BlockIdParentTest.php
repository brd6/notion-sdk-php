<?php

declare(strict_types=1);

namespace Brd6\Test\NotionSdkPhp\Resource\Page\Parent;

use Brd6\NotionSdkPhp\Exception\UnsupportedParentTypeException;
use Brd6\NotionSdkPhp\Resource\Page\Parent\AbstractParentProperty;
use Brd6\NotionSdkPhp\Resource\Page\Parent\BlockIdParent;
use Brd6\Test\NotionSdkPhp\TestCase;

class BlockIdParentTest extends TestCase
{
    public function testToArray(): void
    {
        $blockId = '7d50a184-5bbe-4d90-8f29-6bec57ed817b';

        $parent = (new BlockIdParent())
            ->setBlockId($blockId);

        $this->assertEquals(
            [
                'type' => 'block_id',
                'block_id' => $blockId,
            ],
            $parent->toArray(),
        );
    }

    public function testFromRawData(): void
    {
        $blockId = '7d50a184-5bbe-4d90-8f29-6bec57ed817b';

        $parent = AbstractParentProperty::fromRawData([
            'type' => 'block_id',
            'block_id' => $blockId,
        ]);

        $this->assertInstanceOf(BlockIdParent::class, $parent);
        $this->assertSame($blockId, $parent->getBlockId());
    }

    public function testUnsupportedParentTypeStillThrows(): void
    {
        $this->expectException(UnsupportedParentTypeException::class);

        AbstractParentProperty::fromRawData([
            'type' => 'unsupported_parent',
        ]);
    }
}

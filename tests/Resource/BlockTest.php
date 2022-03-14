<?php

declare(strict_types=1);

namespace Brd6\Test\NotionSdkPhp\Resource;

use Brd6\NotionSdkPhp\Exception\InvalidResourceException;
use Brd6\NotionSdkPhp\Resource\Block;
use PHPUnit\Framework\TestCase;

use function file_get_contents;
use function json_decode;

class BlockTest extends TestCase
{
    public function testInvalidChildPage(): void
    {
        $this->expectException(InvalidResourceException::class);

        Block::fromResponseData([]);
    }

    public function testInvalidChildPageType(): void
    {
        $this->expectException(InvalidResourceException::class);

        Block::fromResponseData([
            'type' => 'invalid_type',
        ]);
    }

    public function testBlock(): void
    {
        $block = Block::fromResponseData(
            (array) json_decode(
                (string) file_get_contents('tests/fixtures/client_blocks_retrieve_block_child_page_200.json'),
                true,
            ),
        );

        $this->assertNotEmpty($block->getType());
        $this->assertNotEmpty($block->getId());
    }
}

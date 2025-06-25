<?php

declare(strict_types=1);

namespace Brd6\Test\NotionSdkPhp\Resource\Pagination;

use Brd6\NotionSdkPhp\Resource\Comment;
use Brd6\NotionSdkPhp\Resource\Pagination\CommentResults;
use Brd6\Test\NotionSdkPhp\TestCase;

use function file_get_contents;
use function json_decode;

class CommentResultsTest extends TestCase
{
    public function testFromRawData(): void
    {
        $rawData = json_decode(
            (string) file_get_contents('tests/Fixtures/comments_list_200.json'),
            true,
        );

        $commentResults = CommentResults::fromRawData($rawData);

        $this->assertEquals('list', $commentResults->getObject());
        $this->assertEquals('comment', $commentResults->getType());
        $this->assertFalse($commentResults->isHasMore());
        $this->assertNull($commentResults->getNextCursor());

        $results = $commentResults->getResults();
        $this->assertCount(2, $results);

        $this->assertInstanceOf(Comment::class, $results[0]);
        $this->assertEquals('7a793800-3e55-4d5e-8009-2261de026179', $results[0]->getId());
        $this->assertEquals('Hello world', $results[0]->getRichText()[0]->getPlainText());

        $this->assertInstanceOf(Comment::class, $results[1]);
        $this->assertEquals('8b3cfeed-c0da-451e-8f18-f7086c321980', $results[1]->getId());
        $this->assertEquals('This is another comment', $results[1]->getRichText()[0]->getPlainText());
    }
}

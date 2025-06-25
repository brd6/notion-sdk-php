<?php

declare(strict_types=1);

namespace Brd6\Test\NotionSdkPhp\Endpoint;

use Brd6\NotionSdkPhp\Client;
use Brd6\NotionSdkPhp\ClientOptions;
use Brd6\NotionSdkPhp\Endpoint\CommentsEndpoint;
use Brd6\NotionSdkPhp\Resource\Comment;
use Brd6\NotionSdkPhp\Resource\Page\Parent\PageIdParent;
use Brd6\NotionSdkPhp\Resource\Pagination\CommentResults;
use Brd6\NotionSdkPhp\Resource\Pagination\PaginationRequest;
use Brd6\NotionSdkPhp\Resource\RichText\Text;
use Brd6\Test\NotionSdkPhp\Mock\HttpClient\MockHttpClient;
use Brd6\Test\NotionSdkPhp\Mock\HttpClient\MockResponseFactory;
use Brd6\Test\NotionSdkPhp\TestCase;

use function file_get_contents;
use function json_decode;

class CommentsEndpointTest extends TestCase
{
    public function testInstance(): void
    {
        $client = new Client();
        $comments = new CommentsEndpoint($client);

        $this->assertInstanceOf(CommentsEndpoint::class, $client->comments());
        $this->assertInstanceOf(CommentsEndpoint::class, $comments);
    }

    public function testRetrieve(): void
    {
        $httpClient = new MockHttpClient(function ($method, $url, $options) {
            $this->assertStringContainsString('GET', $method);
            $this->assertStringContainsString('comments', $url);
            $this->assertStringContainsString('block_id=5c6a2821-6bb1-4a7e-b6e1-c50111515c3d', $url);

            return new MockResponseFactory(
                (string) file_get_contents('tests/Fixtures/comments_list_200.json'),
                [
                    'http_code' => 200,
                ],
            );
        });

        $options = (new ClientOptions())
            ->setAuth('secret_valid-auth')
            ->setHttpClient($httpClient);

        $client = new Client($options);

        $commentResults = $client->comments()->retrieve('5c6a2821-6bb1-4a7e-b6e1-c50111515c3d');

        $this->assertInstanceOf(CommentResults::class, $commentResults);
        $this->assertEquals('list', $commentResults->getObject());
        $this->assertEquals('comment', $commentResults->getType());

        $comments = $commentResults->getResults();
        $this->assertCount(2, $comments);
        $this->assertInstanceOf(Comment::class, $comments[0]);
    }

    public function testRetrieveWithPagination(): void
    {
        $httpClient = new MockHttpClient(function ($method, $url, $options) {
            $this->assertStringContainsString('GET', $method);
            $this->assertStringContainsString('comments', $url);
            $this->assertStringContainsString('block_id=5c6a2821-6bb1-4a7e-b6e1-c50111515c3d', $url);
            $this->assertStringContainsString('page_size=50', $url);
            $this->assertStringContainsString('start_cursor=abc123', $url);

            return new MockResponseFactory(
                (string) file_get_contents('tests/Fixtures/comments_list_200.json'),
                [
                    'http_code' => 200,
                ],
            );
        });

        $options = (new ClientOptions())
            ->setAuth('secret_valid-auth')
            ->setHttpClient($httpClient);

        $client = new Client($options);

        $paginationRequest = (new PaginationRequest())
            ->setPageSize(50)
            ->setStartCursor('abc123');

        $commentResults = $client->comments()->retrieve(
            '5c6a2821-6bb1-4a7e-b6e1-c50111515c3d',
            $paginationRequest,
        );

        $this->assertInstanceOf(CommentResults::class, $commentResults);
    }

    public function testCreatePageComment(): void
    {
        $httpClient = new MockHttpClient(function ($method, $url, $options) {
            $this->assertStringContainsString('POST', $method);
            $this->assertStringContainsString('comments', $url);

            /** @var array $body */
            $body = json_decode($options['body'], true);

            $this->assertArrayHasKey('parent', $body);
            $this->assertArrayHasKey('type', $body['parent']);
            $this->assertArrayHasKey('page_id', $body['parent']);
            $this->assertEquals('page_id', $body['parent']['type']);
            $this->assertEquals('5c6a2821-6bb1-4a7e-b6e1-c50111515c3d', $body['parent']['page_id']);
            $this->assertArrayHasKey('rich_text', $body);
            $this->assertStringContainsString('Hello from integration', $body['rich_text'][0]['text']['content']);

            return new MockResponseFactory(
                (string) file_get_contents('tests/Fixtures/comment_200.json'),
                [
                    'http_code' => 200,
                ],
            );
        });

        $options = (new ClientOptions())
            ->setHttpClient($httpClient);

        $client = new Client($options);

        $comment = new Comment();
        $comment->setParent((new PageIdParent())->setPageId('5c6a2821-6bb1-4a7e-b6e1-c50111515c3d'));
        $comment->setRichText([Text::fromContent('Hello from integration')]);

        $createdComment = $client->comments()->create($comment);

        $this->assertInstanceOf(Comment::class, $createdComment);
        $this->assertEquals('7a793800-3e55-4d5e-8009-2261de026179', $createdComment->getId());
    }

    public function testCreateDiscussionComment(): void
    {
        $httpClient = new MockHttpClient(function ($method, $url, $options) {
            $this->assertStringContainsString('POST', $method);
            $this->assertStringContainsString('comments', $url);

            /** @var array $body */
            $body = json_decode($options['body'], true);

            $this->assertArrayHasKey('discussion_id', $body);
            $this->assertEquals('f4be6752-a539-4da2-a8a9-c3953e13bc0b', $body['discussion_id']);
            $this->assertArrayHasKey('rich_text', $body);
            $this->assertStringContainsString('Reply to discussion', $body['rich_text'][0]['text']['content']);

            return new MockResponseFactory(
                (string) file_get_contents('tests/Fixtures/comment_200.json'),
                [
                    'http_code' => 200,
                ],
            );
        });

        $options = (new ClientOptions())
            ->setHttpClient($httpClient);

        $client = new Client($options);

        $comment = new Comment();
        $comment->setDiscussionId('f4be6752-a539-4da2-a8a9-c3953e13bc0b');
        $comment->setRichText([Text::fromContent('Reply to discussion')]);

        $createdComment = $client->comments()->create($comment);

        $this->assertInstanceOf(Comment::class, $createdComment);
        $this->assertEquals('7a793800-3e55-4d5e-8009-2261de026179', $createdComment->getId());
    }
}

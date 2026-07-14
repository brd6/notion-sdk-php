<?php

declare(strict_types=1);

namespace Brd6\Test\NotionSdkPhp\Endpoint;

use Brd6\NotionSdkPhp\Client;
use Brd6\NotionSdkPhp\ClientOptions;
use Brd6\NotionSdkPhp\Endpoint\AsyncTasksEndpoint;
use Brd6\NotionSdkPhp\Resource\AsyncTask;
use Brd6\Test\NotionSdkPhp\Mock\HttpClient\MockHttpClient;
use Brd6\Test\NotionSdkPhp\Mock\HttpClient\MockResponseFactory;
use Brd6\Test\NotionSdkPhp\TestCase;
use DateTimeImmutable;

use function file_get_contents;

class AsyncTasksEndpointTest extends TestCase
{
    public function testInstance(): void
    {
        $client = new Client();
        $asyncTasks = new AsyncTasksEndpoint($client);

        $this->assertInstanceOf(AsyncTasksEndpoint::class, $client->asyncTasks());
        $this->assertInstanceOf(AsyncTasksEndpoint::class, $asyncTasks);
    }

    public function testRetrieveSucceededAsyncTask(): void
    {
        $httpClient = new MockHttpClient(function (string $method, string $url, array $options) {
            $this->assertEquals('GET', $method);
            $this->assertStringContainsString(
                'async_tasks/task_571ddd84f9564de5aeace9eb4bdcf082',
                $url,
            );

            return new MockResponseFactory(
                (string) file_get_contents('tests/Fixtures/client_async_tasks_retrieve_succeeded_200.json'),
                ['http_code' => 200],
            );
        });

        $client = new Client((new ClientOptions())->setHttpClient($httpClient));

        $asyncTask = $client->asyncTasks()->retrieve('task_571ddd84f9564de5aeace9eb4bdcf082');

        $this->assertEquals(AsyncTask::STATUS_SUCCEEDED, $asyncTask->getStatus());
        $this->assertTrue($asyncTask->isTerminal());
        $this->assertTrue($asyncTask->isSucceeded());
        $this->assertFalse($asyncTask->isFailed());
        $this->assertEquals('page_markdown', $asyncTask->getResult()['object']);
        $this->assertEmpty($asyncTask->getError());
        $this->assertInstanceOf(DateTimeImmutable::class, $asyncTask->getCreatedTime());
        $this->assertNotEmpty($asyncTask->getStatusUrl());
        $this->assertEquals('rest', $asyncTask->getOperation()['surface']);
    }

    public function testRetrieveFailedAsyncTask(): void
    {
        $httpClient = new MockHttpClient(function (string $method, string $url, array $options) {
            return new MockResponseFactory(
                (string) file_get_contents('tests/Fixtures/client_async_tasks_retrieve_failed_200.json'),
                ['http_code' => 200],
            );
        });

        $client = new Client((new ClientOptions())->setHttpClient($httpClient));

        $asyncTask = $client->asyncTasks()->retrieve('task_0baa28ffbe5349a0864d5261d63c8213');

        $this->assertEquals(AsyncTask::STATUS_FAILED, $asyncTask->getStatus());
        $this->assertTrue($asyncTask->isTerminal());
        $this->assertFalse($asyncTask->isSucceeded());
        $this->assertTrue($asyncTask->isFailed());
        $this->assertEquals('validation_error', $asyncTask->getError()['code']);
        $this->assertEmpty($asyncTask->getResult());
    }

    public function testRetrieveQueuedAsyncTask(): void
    {
        $httpClient = new MockHttpClient(function (string $method, string $url, array $options) {
            return new MockResponseFactory(
                (string) file_get_contents('tests/Fixtures/client_pages_update_markdown_202.json'),
                ['http_code' => 200],
            );
        });

        $client = new Client((new ClientOptions())->setHttpClient($httpClient));

        $asyncTask = $client->asyncTasks()->retrieve('task_571ddd84f9564de5aeace9eb4bdcf082');

        $this->assertEquals(AsyncTask::STATUS_QUEUED, $asyncTask->getStatus());
        $this->assertFalse($asyncTask->isTerminal());
        $this->assertEquals(2, $asyncTask->getPollAfterSeconds());
    }
}

<?php

declare(strict_types=1);

namespace Brd6\Test\NotionSdkPhp\Endpoint;

use Brd6\NotionSdkPhp\Client;
use Brd6\NotionSdkPhp\ClientOptions;
use Brd6\NotionSdkPhp\Endpoint\FileUploadsEndpoint;
use Brd6\NotionSdkPhp\Resource\FileUpload;
use Brd6\NotionSdkPhp\Resource\FileUpload\FileUploadListRequest;
use Brd6\NotionSdkPhp\Resource\FileUpload\FileUploadRequest;
use Brd6\NotionSdkPhp\Resource\Pagination\FileUploadResults;
use Brd6\NotionSdkPhp\Resource\Pagination\PaginationRequest;
use Brd6\Test\NotionSdkPhp\Mock\HttpClient\MockHttpClient;
use Brd6\Test\NotionSdkPhp\Mock\HttpClient\MockResponseFactory;
use Brd6\Test\NotionSdkPhp\TestCase;
use DateTimeImmutable;

use function count;
use function file_get_contents;
use function json_decode;

class FileUploadsEndpointTest extends TestCase
{
    public function testInstance(): void
    {
        $client = new Client();
        $fileUploads = new FileUploadsEndpoint($client);

        $this->assertInstanceOf(FileUploadsEndpoint::class, $client->fileUploads());
        $this->assertInstanceOf(FileUploadsEndpoint::class, $fileUploads);
    }

    public function testCreateFileUpload(): void
    {
        $httpClient = new MockHttpClient(function (string $method, string $url, array $options) {
            $this->assertEquals('POST', $method);
            $this->assertStringContainsString('file_uploads', $url);

            /** @var array $body */
            $body = json_decode($options['body'], true);
            $this->assertEquals('single_part', $body['mode']);
            $this->assertEquals('sample.png', $body['filename']);
            $this->assertEquals('image/png', $body['content_type']);
            $this->assertArrayNotHasKey('number_of_parts', $body);
            $this->assertArrayNotHasKey('external_url', $body);

            return new MockResponseFactory(
                (string) file_get_contents('tests/Fixtures/client_file_uploads_create_200.json'),
                ['http_code' => 200],
            );
        });

        $client = new Client((new ClientOptions())->setHttpClient($httpClient));
        $fileUploadRequest = (new FileUploadRequest())
            ->setMode(FileUploadRequest::MODE_SINGLE_PART)
            ->setFilename('sample.png')
            ->setContentType('image/png');

        $fileUpload = $client->fileUploads()->create($fileUploadRequest);

        $this->assertNotEmpty($fileUpload->getId());
        $this->assertEquals(FileUpload::STATUS_PENDING, $fileUpload->getStatus());
        $this->assertInstanceOf(DateTimeImmutable::class, $fileUpload->getExpiryTime());
        $this->assertNotEmpty($fileUpload->getUploadUrl());
        $this->assertFalse($fileUpload->isInTrash());
    }

    public function testCreateExternalUrlFileUpload(): void
    {
        $httpClient = new MockHttpClient(function (string $method, string $url, array $options) {
            /** @var array $body */
            $body = json_decode($options['body'], true);
            $this->assertEquals('external_url', $body['mode']);
            $this->assertEquals('https://example.com/image.png', $body['external_url']);
            $this->assertEquals('image.png', $body['filename']);

            return new MockResponseFactory(
                (string) file_get_contents('tests/Fixtures/client_file_uploads_create_200.json'),
                ['http_code' => 200],
            );
        });

        $client = new Client((new ClientOptions())->setHttpClient($httpClient));
        $fileUploadRequest = (new FileUploadRequest())
            ->setMode(FileUploadRequest::MODE_EXTERNAL_URL)
            ->setFilename('image.png')
            ->setExternalUrl('https://example.com/image.png');

        $fileUpload = $client->fileUploads()->create($fileUploadRequest);

        $this->assertEquals(FileUpload::STATUS_PENDING, $fileUpload->getStatus());
    }

    public function testCreateMultiPartFileUpload(): void
    {
        $httpClient = new MockHttpClient(function (string $method, string $url, array $options) {
            /** @var array $body */
            $body = json_decode($options['body'], true);
            $this->assertEquals('multi_part', $body['mode']);
            $this->assertEquals(2, $body['number_of_parts']);

            return new MockResponseFactory(
                (string) file_get_contents('tests/Fixtures/client_file_uploads_create_multi_part_200.json'),
                ['http_code' => 200],
            );
        });

        $client = new Client((new ClientOptions())->setHttpClient($httpClient));
        $fileUploadRequest = (new FileUploadRequest())
            ->setMode(FileUploadRequest::MODE_MULTI_PART)
            ->setFilename('sample-large.txt')
            ->setContentType('text/plain')
            ->setNumberOfParts(2);

        $fileUpload = $client->fileUploads()->create($fileUploadRequest);

        $this->assertEquals(['total' => 2, 'sent' => 0], $fileUpload->getNumberOfParts());
        $this->assertNotEmpty($fileUpload->getCompleteUrl());
    }

    public function testSendFileUpload(): void
    {
        $httpClient = new MockHttpClient(function (string $method, string $url, array $options) {
            $this->assertEquals('POST', $method);
            $this->assertStringContainsString(
                'file_uploads/b52b8ed6-e029-4707-a671-832549c09de3/send',
                $url,
            );
            $this->assertStringStartsWith(
                'multipart/form-data; boundary=',
                $options['headers']['Content-Type'][0],
            );
            $this->assertStringContainsString('name="file"', $options['body']);
            $this->assertStringContainsString('filename="sample.png"', $options['body']);
            $this->assertStringContainsString('Content-Type: image/png', $options['body']);
            $this->assertStringContainsString('file-contents', $options['body']);
            $this->assertStringNotContainsString('part_number', $options['body']);

            return new MockResponseFactory(
                (string) file_get_contents('tests/Fixtures/client_file_uploads_send_200.json'),
                ['http_code' => 200],
            );
        });

        $client = new Client((new ClientOptions())->setHttpClient($httpClient));

        $fileUpload = $client->fileUploads()->send(
            'b52b8ed6-e029-4707-a671-832549c09de3',
            'file-contents',
            'sample.png',
            'image/png',
        );

        $this->assertEquals(FileUpload::STATUS_UPLOADED, $fileUpload->getStatus());
        $this->assertEquals(70, $fileUpload->getContentLength());
    }

    public function testSendFileUploadPart(): void
    {
        $httpClient = new MockHttpClient(function (string $method, string $url, array $options) {
            $this->assertStringContainsString('name="part_number"', $options['body']);
            $this->assertStringContainsString('2', $options['body']);

            return new MockResponseFactory(
                (string) file_get_contents('tests/Fixtures/client_file_uploads_send_200.json'),
                ['http_code' => 200],
            );
        });

        $client = new Client((new ClientOptions())->setHttpClient($httpClient));

        $fileUpload = $client->fileUploads()->send(
            'b52b8ed6-e029-4707-a671-832549c09de3',
            'part-contents',
            'sample-large.txt',
            'text/plain',
            2,
        );

        $this->assertEquals(FileUpload::STATUS_UPLOADED, $fileUpload->getStatus());
    }

    public function testCompleteFileUpload(): void
    {
        $httpClient = new MockHttpClient(function (string $method, string $url, array $options) {
            $this->assertEquals('POST', $method);
            $this->assertStringContainsString(
                'file_uploads/a3f5c1d2-77b4-4e08-9c26-1b52f8d90a14/complete',
                $url,
            );

            return new MockResponseFactory(
                (string) file_get_contents('tests/Fixtures/client_file_uploads_complete_200.json'),
                ['http_code' => 200],
            );
        });

        $client = new Client((new ClientOptions())->setHttpClient($httpClient));

        $fileUpload = $client->fileUploads()->complete('a3f5c1d2-77b4-4e08-9c26-1b52f8d90a14');

        $this->assertEquals(FileUpload::STATUS_UPLOADED, $fileUpload->getStatus());
        $this->assertEquals(['total' => 2, 'sent' => 2], $fileUpload->getNumberOfParts());
    }

    public function testRetrieveFileUpload(): void
    {
        $httpClient = new MockHttpClient(function (string $method, string $url, array $options) {
            $this->assertEquals('GET', $method);
            $this->assertStringContainsString(
                'file_uploads/c7e2a9b8-3d61-4f5a-8e07-92c4d1f6b3a5',
                $url,
            );

            return new MockResponseFactory(
                (string) file_get_contents('tests/Fixtures/client_file_uploads_retrieve_200.json'),
                ['http_code' => 200],
            );
        });

        $client = new Client((new ClientOptions())->setHttpClient($httpClient));

        $fileUpload = $client->fileUploads()->retrieve('c7e2a9b8-3d61-4f5a-8e07-92c4d1f6b3a5');

        $this->assertEquals(FileUpload::STATUS_UPLOADED, $fileUpload->getStatus());
        $this->assertEquals('success', $fileUpload->getFileImportResult()['type']);
        $this->assertEquals(['id' => '6794760a-1f15-45cd-9c65-0dfe42f5135a', 'type' => 'bot'], $fileUpload->getCreatedBy());
    }

    public function testListFileUploads(): void
    {
        $httpClient = new MockHttpClient(function (string $method, string $url, array $options) {
            $this->assertEquals('GET', $method);
            $this->assertStringContainsString('file_uploads', $url);
            $this->assertEquals('uploaded', $options['query']['status']);
            $this->assertEquals(2, (int) $options['query']['page_size']);

            return new MockResponseFactory(
                (string) file_get_contents('tests/Fixtures/client_file_uploads_list_200.json'),
                ['http_code' => 200],
            );
        });

        $client = new Client((new ClientOptions())->setHttpClient($httpClient));

        $listRequest = (new FileUploadListRequest())->setStatus(FileUpload::STATUS_UPLOADED);
        $paginationRequest = (new PaginationRequest())->setPageSize(2);

        $results = $client->fileUploads()->list($listRequest, $paginationRequest);

        $this->assertInstanceOf(FileUploadResults::class, $results);
        $this->assertGreaterThan(0, count($results->getResults()));
        $this->assertInstanceOf(FileUpload::class, $results->getResults()[0]);
        $this->assertTrue($results->isHasMore());
        $this->assertNotEmpty($results->getNextCursor());
    }
}

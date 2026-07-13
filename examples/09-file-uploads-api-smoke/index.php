<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use Brd6\NotionSdkPhp\Client;
use Brd6\NotionSdkPhp\ClientOptions;
use Brd6\NotionSdkPhp\Exception\ApiResponseException;
use Brd6\NotionSdkPhp\RequestParameters;
use Dotenv\Dotenv;
use Http\Message\MultipartStream\MultipartStreamBuilder;

// 240x120 labelled PNG so the example needs no file on disk and the block is visible on the page.
const SAMPLE_PNG_BASE64 = 'iVBORw0KGgoAAAANSUhEUgAAAPAAAAB4CAIAAABD1OhwAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAB/ElEQVR42u3YMW7CMABA0VJxDSY4JhsckwkuwhApch3bMTYtSfOeOlREcUz4dU12x8v9C/6Lb7cAQYOgQdAgaAQNggZBg6BB0AgaBA2CBkGDoBE0CBoEDYIGQSNoEDQIGgQNgkbQIGgQNAgaBI2gQdAgaBA0CBpBg6BB0CBoBA2CBkGDoEHQCBoEDYIGQYOgETQIGgQNggZBI+gtu50Pt/NhmfN579yW9k4F7YPkh71bMHW6PtwEQX9glR3iG9faKMRwDR4PhS+OIyTPmmZdGLAwjfL8c39INaMl599wo5KXa35fthy9WZ+uj+FeJ2ONDo2vRL/PLs+5AcvTmI1s/AlPDI/mRqusuWaGbYeWaXe83Ne+QueWouShmhpmT6+8Vq6A5FmVV0/+lyjUVjPDtntoy2EvHncWVdjTin3/pp9yLCT33JajMl8ParYSdG5LmttgzJbx0oCV+6XCJmF6rf6m2yb/xrdsy9HbdBhN9EnkjhaegZQH7JxeNGZ4dHYvPnxvq7ziq5Nf3VOOFX8ppPnb8++dZcsBVmiwQiNoEDQIGgQNgkbQIGgQNAgaBI2gQdAgaBA0CBpBg6BB0CBoBA2CBkGDoEHQCBoEDYIGQYOgETQIGgQNggZBI2gQNAgaBA2CRtAgaBA0CBoEjaBB0CBoEDQIGkGDoOHPPAGafD74akpkogAAAABJRU5ErkJggg==';

function exitWithError(string $message): void
{
    echo "Error: {$message}\n";
    exit(1);
}

function loadEnvironmentVariables(): void
{
    if (!file_exists(__DIR__ . '/.env')) {
        exitWithError('Missing .env file. Copy .env.example to .env and configure it.');
    }

    Dotenv::createImmutable(__DIR__)->load();
}

function validateRequiredEnvironmentVariables(): void
{
    $requiredVars = ['NOTION_TOKEN', 'NOTION_PAGE_ID'];

    foreach ($requiredVars as $requiredVar) {
        if (empty($_ENV[$requiredVar])) {
            exitWithError("Missing required environment variable: {$requiredVar}");
        }
    }
}

function createNotionClient(): Client
{
    $options = (new ClientOptions())
        ->setAuth($_ENV['NOTION_TOKEN']);

    return new Client($options);
}

function createFileUpload(Client $notion, string $filename): array
{
    $parameters = (new RequestParameters())
        ->setPath('file_uploads')
        ->setMethod('POST')
        ->setBody([
            'mode' => 'single_part',
            'filename' => $filename,
            'content_type' => 'image/png',
        ]);

    return $notion->request($parameters);
}

function sendFileUpload(Client $notion, string $fileUploadId, string $contents, string $filename): array
{
    $builder = new MultipartStreamBuilder();
    $builder->addResource('file', $contents, [
        'filename' => $filename,
        'headers' => ['Content-Type' => 'image/png'],
    ]);

    $parameters = (new RequestParameters())
        ->setPath("file_uploads/{$fileUploadId}/send")
        ->setMethod('POST')
        ->setHeaders([
            'Content-Type' => 'multipart/form-data; boundary="' . $builder->getBoundary() . '"',
        ])
        ->setRawBody((string) $builder->build());

    return $notion->request($parameters);
}

function retrieveFileUpload(Client $notion, string $fileUploadId): array
{
    $parameters = (new RequestParameters())
        ->setPath("file_uploads/{$fileUploadId}")
        ->setMethod('GET');

    return $notion->request($parameters);
}

function attachFileUploadToPage(Client $notion, string $pageId, string $fileUploadId): array
{
    $parameters = (new RequestParameters())
        ->setPath("blocks/{$pageId}/children")
        ->setMethod('PATCH')
        ->setBody([
            'children' => [
                [
                    'object' => 'block',
                    'type' => 'image',
                    'image' => [
                        'type' => 'file_upload',
                        'file_upload' => [
                            'id' => $fileUploadId,
                        ],
                    ],
                ],
            ],
        ]);

    return $notion->request($parameters);
}

function main(): void
{
    loadEnvironmentVariables();
    validateRequiredEnvironmentVariables();

    $notion = createNotionClient();
    $pageId = (string) $_ENV['NOTION_PAGE_ID'];
    $filename = 'sample.png';
    $contents = (string) base64_decode(SAMPLE_PNG_BASE64, true);

    try {
        echo "Creating file upload...\n";
        $fileUpload = createFileUpload($notion, $filename);
        $fileUploadId = (string) $fileUpload['id'];
        echo "File upload created: {$fileUploadId} (status: {$fileUpload['status']})\n";

        echo "Sending file contents as multipart/form-data...\n";
        $fileUpload = sendFileUpload($notion, $fileUploadId, $contents, $filename);
        echo "File contents sent (status: {$fileUpload['status']})\n";

        $fileUpload = retrieveFileUpload($notion, $fileUploadId);
        if ($fileUpload['status'] !== 'uploaded') {
            exitWithError("Unexpected file upload status: {$fileUpload['status']}");
        }
        echo "File upload verified (status: {$fileUpload['status']})\n";

        echo "Attaching the uploaded image to the page...\n";
        $result = attachFileUploadToPage($notion, $pageId, $fileUploadId);
        $blockId = (string) $result['results'][0]['id'];
        echo "Image block created: {$blockId}\n";

        echo "Done. Check the page in Notion to see the uploaded image.\n";
    } catch (ApiResponseException $exception) {
        exitWithError("Notion API error ({$exception->getMessageCode()}): {$exception->getMessage()}");
    }
}

main();

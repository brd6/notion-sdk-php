<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use Brd6\NotionSdkPhp\Client;
use Brd6\NotionSdkPhp\ClientOptions;
use Brd6\NotionSdkPhp\Exception\ApiResponseException;
use Brd6\NotionSdkPhp\Resource\Block\ImageBlock;
use Brd6\NotionSdkPhp\Resource\File\FileUpload as FileUploadFile;
use Brd6\NotionSdkPhp\Resource\FileUpload;
use Brd6\NotionSdkPhp\Resource\Property\FileUploadProperty;
use Dotenv\Dotenv;

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

function buildImageBlock(string $fileUploadId): ImageBlock
{
    $image = new FileUploadFile();
    $image->setFileUpload((new FileUploadProperty())->setId($fileUploadId));

    return (new ImageBlock())->setImage($image);
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
        echo "Uploading {$filename}...\n";
        $fileUpload = $notion->fileUploads()->upload($contents, $filename);
        echo "File uploaded: {$fileUpload->getId()} (status: {$fileUpload->getStatus()})\n";

        $fileUpload = $notion->fileUploads()->retrieve($fileUpload->getId());
        if ($fileUpload->getStatus() !== FileUpload::STATUS_UPLOADED) {
            exitWithError("Unexpected file upload status: {$fileUpload->getStatus()}");
        }
        echo "File upload verified (status: {$fileUpload->getStatus()})\n";

        echo "Attaching the uploaded image to the page...\n";
        $results = $notion->blocks()->children()->append($pageId, [buildImageBlock($fileUpload->getId())]);

        /** @var ImageBlock $block */
        $block = $results->getResults()[0];
        echo "Image block created: {$block->getId()}\n";

        echo "Listing recent uploads...\n";
        $uploads = $notion->fileUploads()->list();
        echo "Uploads found: " . count($uploads->getResults()) . "\n";

        echo "Done. Check the page in Notion to see the uploaded image.\n";
    } catch (ApiResponseException $exception) {
        exitWithError("Notion API error ({$exception->getMessageCode()}): {$exception->getMessage()}");
    }
}

main();

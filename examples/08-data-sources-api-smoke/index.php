<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use Brd6\NotionSdkPhp\Client;
use Brd6\NotionSdkPhp\ClientOptions;
use Brd6\NotionSdkPhp\Exception\ApiResponseException;
use Brd6\NotionSdkPhp\Resource\DataSource;
use Brd6\NotionSdkPhp\Resource\Database\DatabaseRequest;
use Brd6\NotionSdkPhp\Resource\Database\PropertyObject\TitlePropertyObject;
use Brd6\NotionSdkPhp\Resource\Page\Parent\DatabaseIdParent;
use Brd6\NotionSdkPhp\Resource\Pagination\PaginationRequest;
use Brd6\NotionSdkPhp\Resource\RichText\Text;
use Brd6\NotionSdkPhp\Resource\Search\SearchRequest;
use Dotenv\Dotenv;

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
    $requiredVars = ['NOTION_TOKEN', 'NOTION_DATABASE_ID'];

    foreach ($requiredVars as $requiredVar) {
        if (empty($_ENV[$requiredVar])) {
            exitWithError("Missing required environment variable: {$requiredVar}");
        }
    }
}

function createNotionClient(): Client
{
    $options = (new ClientOptions())
        ->setAuth($_ENV['NOTION_TOKEN'])
        ->setNotionVersion('2025-09-03');

    return new Client($options);
}

function isWriteModeEnabled(): bool
{
    $val = getenv('NOTION_RUN_WRITES') ?: ($_ENV['NOTION_RUN_WRITES'] ?? '0');
    return in_array((string) $val, ['1', 'true', 'yes'], true);
}

function resolveDataSourceId(Client $notion, string $databaseId): string
{
    $database = $notion->databases()->retrieve($databaseId);
    $dataSources = $database->getDataSources();

    echo "Database retrieved: {$database->getId()}\n";
    echo "Data sources linked to database: " . count($dataSources) . "\n";

    $envDataSourceId = (string) ($_ENV['NOTION_DATA_SOURCE_ID'] ?? '');
    if ($envDataSourceId !== '') {
        echo "Using data source from NOTION_DATA_SOURCE_ID.\n";
        return $envDataSourceId;
    }

    if (!isset($dataSources[0])) {
        exitWithError('No data source found in the database response. Set NOTION_DATA_SOURCE_ID manually.');
    }

    $resolvedDataSourceId = $dataSources[0]->getId();
    echo "Using first discovered data source id: {$resolvedDataSourceId}\n";

    return $resolvedDataSourceId;
}

function runReadOnlyChecks(Client $notion, string $dataSourceId): void
{
    $dataSource = $notion->dataSources()->retrieve($dataSourceId);
    echo "Data source retrieved: {$dataSource->getId()}\n";

    $queryRequest = (new DatabaseRequest())->setFilter([
        'property' => 'Name',
        'title' => ['is_not_empty' => true],
    ]);

    $paginationRequest = (new PaginationRequest())->setPageSize(1);
    $queryResults = $notion->dataSources()->query($dataSourceId, $queryRequest, $paginationRequest);
    echo "Data source query ok. Returned pages: " . count($queryResults->getResults()) . "\n";

    $searchRequest = (new SearchRequest())->setFilter([
        'property' => 'object',
        'value' => 'data_source',
    ]);
    $searchResults = $notion->search($searchRequest);
    echo "Search (object=data_source) ok. Returned items: " . count($searchResults->getResults()) . "\n";
}

function runWriteChecks(Client $notion, string $databaseId): void
{
    echo "Write mode is enabled. Running create/update checks...\n";

    $baseName = 'SDK Integration Check ' . date('Y-m-d H:i:s');
    $created = $notion->dataSources()->create(
        (new DataSource())
            ->setParent((new DatabaseIdParent())->setDatabaseId($databaseId))
            ->setTitle([Text::fromContent($baseName)])
            ->setProperties(['Name' => new TitlePropertyObject()]),
    );
    echo "Data source created: {$created->getId()}\n";

    $dataSourceForUpdate = new DataSource();
    $dataSourceForUpdate->setId($created->getId());

    $updated = $notion->dataSources()->update(
        $dataSourceForUpdate
            ->setTitle([Text::fromContent($baseName . ' Updated')])
            ->setProperties(['Name' => new TitlePropertyObject()])
            ->setInTrash(true),
    );
    echo 'Data source updated. In trash: ' . ($updated->isInTrash() ? 'yes' : 'no') . "\n";
}

function main(): void
{
    try {
        echo "Notion SDK PHP - Data Sources API Integration\n";
        echo "=======================================\n\n";

        loadEnvironmentVariables();
        validateRequiredEnvironmentVariables();

        $notion = createNotionClient();
        $databaseId = (string) $_ENV['NOTION_DATABASE_ID'];

        $dataSourceId = resolveDataSourceId($notion, $databaseId);
        runReadOnlyChecks($notion, $dataSourceId);

        if (isWriteModeEnabled()) {
            runWriteChecks($notion, $databaseId);
        } else {
            echo "Write mode disabled. Skipping create/update checks.\n";
        }

        echo "\nIntegration checks completed.\n";
    } catch (ApiResponseException $exception) {
        exitWithError('Notion API error: ' . $exception->getMessage());
    } catch (Exception $exception) {
        exitWithError($exception->getMessage());
    }
}

main();

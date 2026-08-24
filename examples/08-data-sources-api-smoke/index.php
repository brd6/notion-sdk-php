<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use Brd6\NotionSdkPhp\Client;
use Brd6\NotionSdkPhp\ClientOptions;
use Brd6\NotionSdkPhp\Exception\ApiResponseException;
use Brd6\NotionSdkPhp\Resource\DataSource;
use Brd6\NotionSdkPhp\Resource\Database\DatabaseRequest;
use Brd6\NotionSdkPhp\Resource\Database\PropertyObject\PlacePropertyObject;
use Brd6\NotionSdkPhp\Resource\Database\PropertyObject\TitlePropertyObject;
use Brd6\NotionSdkPhp\Resource\Page;
use Brd6\NotionSdkPhp\Resource\Page\Parent\DataSourceIdParent;
use Brd6\NotionSdkPhp\Resource\Page\Parent\DatabaseIdParent;
use Brd6\NotionSdkPhp\Resource\Page\PropertyValue\PlacePropertyValue;
use Brd6\NotionSdkPhp\Resource\Page\PropertyValue\TitlePropertyValue;
use Brd6\NotionSdkPhp\Resource\Pagination\PaginationRequest;
use Brd6\NotionSdkPhp\Resource\Property\PlaceProperty;
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
            ->setProperties([
                'Name' => new TitlePropertyObject(),
                'Location' => new PlacePropertyObject(),
            ]),
    );
    echo "Data source created: {$created->getId()}\n";

    try {
        runPlacePageChecks($notion, $created->getId());
    } finally {
        $dataSourceForUpdate = new DataSource();
        $dataSourceForUpdate->setId($created->getId());

        $updated = $notion->dataSources()->update(
            $dataSourceForUpdate
                ->setTitle([Text::fromContent($baseName . ' Updated')])
                ->setInTrash(true),
        );
        echo 'Data source updated. In trash: ' . ($updated->isInTrash() ? 'yes' : 'no') . "\n";
    }
}

function runPlacePageChecks(Client $notion, string $dataSourceId): void
{
    $createdPlace = (new PlaceProperty())
        ->setLat(48.8584)
        ->setLon(2.2945)
        ->setName('Eiffel Tower')
        ->setAddress('5 Avenue Anatole France, 75007 Paris');
    $page = (new Page())
        ->setParent((new DataSourceIdParent())->setDataSourceId($dataSourceId))
        ->setProperties([
            'Name' => (new TitlePropertyValue())->setTitle([Text::fromContent('Place write check')]),
            'Location' => (new PlacePropertyValue())->setPlace($createdPlace),
        ]);

    $createdPage = $notion->pages()->create($page);
    echo "Page with Place created: {$createdPage->getId()}\n";

    $retrievedPage = $notion->pages()->retrieve($createdPage->getId());
    assertPlace($retrievedPage, 48.8584, 2.2945, 'Eiffel Tower', '5 Avenue Anatole France, 75007 Paris');
    echo "Populated Place create and read check passed.\n";

    $updatedPlace = (new PlaceProperty())
        ->setLat(48.8606)
        ->setLon(2.3376)
        ->setName('Louvre Museum')
        ->setAddress('Rue de Rivoli, 75001 Paris');
    $pageForUpdate = (new Page())
        ->setId($createdPage->getId())
        ->setProperties([
            'Location' => (new PlacePropertyValue())->setPlace($updatedPlace),
        ]);

    $updatedPage = $notion->pages()->update($pageForUpdate);
    assertPlace($updatedPage, 48.8606, 2.3376, 'Louvre Museum', 'Rue de Rivoli, 75001 Paris');
    echo "Populated Place update check passed.\n";
}

function assertPlace(Page $page, float $lat, float $lon, string $name, string $address): void
{
    $propertyValue = $page->getProperties()['Location'] ?? null;
    if (!$propertyValue instanceof PlacePropertyValue) {
        throw new RuntimeException('Location did not hydrate as PlacePropertyValue.');
    }

    $place = $propertyValue->getPlace();
    if ($place === null) {
        throw new RuntimeException('Location hydrated with a null Place value.');
    }

    if (abs((float) $place->getLat() - $lat) > 0.000001 || abs((float) $place->getLon() - $lon) > 0.000001) {
        throw new RuntimeException('Location coordinates do not match the populated write.');
    }

    if ($place->getName() !== $name) {
        throw new RuntimeException('Location name does not match the populated write.');
    }

    if ($place->getAddress() !== $address) {
        throw new RuntimeException('Location address does not match the populated write.');
    }
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

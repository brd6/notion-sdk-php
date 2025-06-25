<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use Brd6\NotionSdkPhp\Client;
use Brd6\NotionSdkPhp\ClientOptions;
use Brd6\NotionSdkPhp\Exception\ApiResponseException;
use Brd6\NotionSdkPhp\Resource\Page;
use Brd6\NotionSdkPhp\Resource\Page\Parent\DatabaseIdParent;
use Brd6\NotionSdkPhp\Resource\Page\PropertyValue\TitlePropertyValue;
use Brd6\NotionSdkPhp\Resource\RichText\Text;
use Dotenv\Dotenv;

function loadEnvironmentVariables(): void
{
    if (!file_exists(__DIR__ . '/.env')) {
        echo "Error: .env file not found!\n";
        echo "Please create a .env file by copying env.example:\n";
        echo "  cp .env.example .env\n";
        echo "Then add your Notion token and database ID to the .env file.\n";
        exit(1);
    }
    
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}

function validateRequiredEnvironmentVariables(): void
{
    $requiredVars = ['NOTION_TOKEN', 'NOTION_DATABASE_ID'];
    
    foreach ($requiredVars as $var) {
        if (empty($_ENV[$var])) {
            echo "Error: Environment variable $var is required but not set.\n";
            echo "Please check your .env file and ensure all variables are configured.\n";
            exit(1);
        }
    }
}

function createNotionClient(): Client
{
    $options = (new ClientOptions())->setAuth($_ENV['NOTION_TOKEN']);
    return new Client($options);
}

function listDatabasePages(Client $notion, string $databaseId): void
{
    echo "Fetching pages from your database...\n\n";
    
    $results = $notion->databases()->query($databaseId);
    $pages = $results->getResults();
    
    if (empty($pages)) {
        echo "No pages found in the database.\n";
        return;
    }
    
    echo "Found " . count($pages) . " page(s):\n";
    foreach ($pages as $page) {
        $properties = $page->getProperties();
        $title = 'Untitled';
        
        foreach ($properties as $propertyName => $property) {
            if ($property instanceof TitlePropertyValue) {
                $titleRichTexts = $property->getTitle();
                if (!empty($titleRichTexts)) {
                    $title = $titleRichTexts[0]->getText()?->getContent() ?? 'Untitled';
                }
                break;
            }
        }
        
        echo "  - $title\n";
    }
    
    echo "\n";
}

function getTitlePropertyName(Client $notion, string $databaseId): ?string
{
    $database = $notion->databases()->retrieve($databaseId);
    $properties = $database->getProperties();
    
    foreach ($properties as $propertyName => $property) {
        if ($property->getType() === 'title') {
            return $propertyName;
        }
    }
    
    return null;
}

function createNewPage(Client $notion, string $databaseId): void
{
    echo "Creating a new page in your database...\n";
    
    $titlePropertyName = getTitlePropertyName($notion, $databaseId);
    
    if ($titlePropertyName === null) {
        echo "Error: No title property found in the database.\n";
        return;
    }
    
    $page = new Page();
    
    $parent = (new DatabaseIdParent())->setDatabaseId($databaseId);
    $page->setParent($parent);
    
    $titleContent = 'New Entry from PHP SDK - ' . date('Y-m-d H:i:s');
    $titleProperty = (new TitlePropertyValue())->setTitle([Text::fromContent($titleContent)]);
    
    $page->setProperties([$titlePropertyName => $titleProperty]);
    
    $createdPage = $notion->pages()->create($page);
    
    echo "Successfully created new page!\n";
    echo "Title: $titleContent\n"; 
    echo "Page URL: " . $createdPage->getUrl() . "\n";
}

function main(): void
{
    try {
        echo "Notion SDK PHP - Basic Example\n";
        echo "===============================\n\n";
        
        loadEnvironmentVariables();
        validateRequiredEnvironmentVariables();
        
        $notion = createNotionClient();
        $databaseId = $_ENV['NOTION_DATABASE_ID'];
        
        listDatabasePages($notion, $databaseId);
        createNewPage($notion, $databaseId);
        
        echo "\nExample completed successfully!\n";
        
    } catch (ApiResponseException $e) {
        echo "Notion API Error: " . $e->getMessage() . "\n";
        echo "Please check your token and database ID, and ensure the database is shared with your integration.\n";
        exit(1);
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
        exit(1);
    }
}

main(); 
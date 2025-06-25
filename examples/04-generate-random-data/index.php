<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use Brd6\NotionSdkPhp\Client;
use Brd6\NotionSdkPhp\ClientOptions;
use Brd6\NotionSdkPhp\Exception\ApiResponseException;
use Brd6\NotionSdkPhp\Resource\Page;
use Brd6\NotionSdkPhp\Resource\Page\Parent\DatabaseIdParent;
use Brd6\NotionSdkPhp\Resource\Page\PropertyValue\CheckboxPropertyValue;
use Brd6\NotionSdkPhp\Resource\Page\PropertyValue\DatePropertyValue;
use Brd6\NotionSdkPhp\Resource\Page\PropertyValue\EmailPropertyValue;
use Brd6\NotionSdkPhp\Resource\Page\PropertyValue\NumberPropertyValue;
use Brd6\NotionSdkPhp\Resource\Page\PropertyValue\RichTextPropertyValue;
use Brd6\NotionSdkPhp\Resource\Page\PropertyValue\SelectPropertyValue;
use Brd6\NotionSdkPhp\Resource\Page\PropertyValue\TitlePropertyValue;
use Brd6\NotionSdkPhp\Resource\Page\PropertyValue\UrlPropertyValue;
use Brd6\NotionSdkPhp\Resource\Property\DateProperty;
use Brd6\NotionSdkPhp\Resource\Property\SelectProperty;
use Brd6\NotionSdkPhp\Resource\RichText\Text;
use Dotenv\Dotenv;
use Faker\Factory;
use Faker\Generator;

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

function parseCommandLineArguments(): int
{
    global $argv;
    
    if (isset($argv[1])) {
        $count = (int) $argv[1];
        if ($count <= 0) {
            echo "Error: Count must be a positive number.\n";
            exit(1);
        }
        return $count;
    }
    
    return 10; // Default count
}

function detectDatabaseSchema(Client $notion, string $databaseId): array
{
    echo "Analyzing database schema...\n";
    
    $database = $notion->databases()->retrieve($databaseId);
    $properties = $database->getProperties();
    
    $schema = [];
    $propertyNames = [];
    
    foreach ($properties as $propertyName => $property) {
        $propertyType = $property->getType();
        $schema[$propertyName] = [
            'type' => $propertyType,
            'config' => $property
        ];
        $propertyNames[] = "$propertyName ($propertyType)";
    }
    
    echo "Found " . count($schema) . " properties: " . implode(', ', $propertyNames) . "\n\n";
    
    return $schema;
}

function generatePropertyValue(string $propertyName, array $propertyData, $faker): ?object
{
    $type = $propertyData['type'];
    $config = $propertyData['config'];
    
    switch ($type) {
        case 'title':
            $titleProperty = (new TitlePropertyValue())
                ->setTitle([Text::fromContent($faker->company . ' - ' . $faker->catchPhrase)]);
            return $titleProperty;
            
        case 'rich_text':
            $richTextProperty = (new RichTextPropertyValue())
                ->setRichText([Text::fromContent($faker->paragraph)]);
            return $richTextProperty;
            
        case 'number':
            $numberProperty = (new NumberPropertyValue())
                ->setNumber($faker->randomFloat(2, 0, 10000));
            return $numberProperty;
            
        case 'select':
            $options = $config->getOptions();
            if (!empty($options)) {
                $selectedOption = $faker->randomElement($options);
                $selectProperty = (new SelectPropertyValue())
                    ->setSelect($selectedOption);
                return $selectProperty;
            }
            return null;
            
        case 'checkbox':
            $checkboxProperty = (new CheckboxPropertyValue())
                ->setCheckbox($faker->boolean);
            return $checkboxProperty;
            
        case 'email':
            $emailProperty = (new EmailPropertyValue())
                ->setEmail($faker->email);
            return $emailProperty;
            
        case 'url':
            $urlProperty = (new UrlPropertyValue())
                ->setUrl($faker->url);
            return $urlProperty;
            
        case 'date':
            $dateProperty = (new DatePropertyValue())
                ->setDate((new DateProperty())->setStart($faker->date('Y-m-d')));
            return $dateProperty;
            
        default:
            return null;
    }
}

function createRandomPage(array $schema, string $databaseId, $faker): Page
{
    $page = new Page();
    $parent = (new DatabaseIdParent())->setDatabaseId($databaseId);
    $page->setParent($parent);
    
    $pageProperties = [];
    
    foreach ($schema as $propertyName => $propertyData) {
        $propertyValue = generatePropertyValue($propertyName, $propertyData, $faker);
        
        if ($propertyValue !== null) {
            $pageProperties[$propertyName] = $propertyValue;
        }
    }
    
    $page->setProperties($pageProperties);
    
    return $page;
}

function showProgress(int $current, int $total): void
{
    $percentage = round(($current / $total) * 100);
    $progressWidth = 50;
    $filledWidth = (int) round(($percentage / 100) * $progressWidth);
    
    $progressBar = str_repeat('█', $filledWidth) . str_repeat('░', $progressWidth - $filledWidth);
    
    echo "\rProgress: [$progressBar] $current/$total ($percentage%)";
    
    if ($current >= $total) {
        echo "\n\n";
    }
}

function generateRandomData(Client $notion, string $databaseId, int $count): void
{
    $schema = detectDatabaseSchema($notion, $databaseId);
    
    if (empty($schema)) {
        echo "Error: Database has no properties. Please add some properties to your database first.\n";
        exit(1);
    }
    
    $faker = \Faker\Factory::create();
    
    echo "Generating $count random entries...\n";
    
    $created = 0;
    $errors = 0;
    
    for ($i = 1; $i <= $count; $i++) {
        try {
            $page = createRandomPage($schema, $databaseId, $faker);
            $notion->pages()->create($page);
            $created++;
            
        } catch (ApiResponseException $e) {
            $errors++;
            if ($errors <= 3) {
                echo "\nWarning: Failed to create entry $i: " . $e->getMessage() . "\n";
            } elseif ($errors === 4) {
                echo "\n(Suppressing further error messages...)\n";
            }
        } catch (Exception $e) {
            $errors++;
            if ($errors <= 3) {
                echo "\nError creating entry $i: " . $e->getMessage() . "\n";
            }
        }
        
        showProgress($i, $count);
        
        // Small delay to avoid rate limiting
        usleep(100000); // 0.1 seconds
    }
    
    echo "Successfully created $created entries";
    if ($errors > 0) {
        echo " ($errors failed)";
    }
    echo " in your Notion database!\n";
}

function main(): void
{
    try {
        echo "Notion SDK PHP - Random Data Generator\n";
        echo "=====================================\n\n";
        
        loadEnvironmentVariables();
        validateRequiredEnvironmentVariables();
        
        $notion = createNotionClient();
        $databaseId = $_ENV['NOTION_DATABASE_ID'];
        $count = parseCommandLineArguments();
        
        generateRandomData($notion, $databaseId, $count);
        
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
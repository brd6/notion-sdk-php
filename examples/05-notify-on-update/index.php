<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use Brd6\NotionSdkPhp\Client;
use Brd6\NotionSdkPhp\ClientOptions;
use Brd6\NotionSdkPhp\Exception\ApiResponseException;
use Brd6\NotionSdkPhp\Resource\Page\PropertyValue\SelectPropertyValue;
use Brd6\NotionSdkPhp\Resource\Page\PropertyValue\StatusPropertyValue;
use Brd6\NotionSdkPhp\Resource\Page\PropertyValue\TitlePropertyValue;
use Brd6\NotionSdkPhp\Resource\Pagination\PaginationRequest;
use Dotenv\Dotenv;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;

const STATE_FILE = __DIR__ . '/last_check.txt';

// Environment Management
function loadEnvironment(): void
{
    if (!file_exists(__DIR__ . '/.env')) {
        exitWithError('Missing .env file. Copy .env.example to .env and configure it.');
    }
    
    Dotenv::createImmutable(__DIR__)->load();
}

function validateEnvironment(bool $requireEmail = false): void
{
    $required = ['NOTION_TOKEN', 'NOTION_DATABASE_ID'];
    
    if ($requireEmail) {
        $required = array_merge($required, ['SMTP_HOST', 'SMTP_PORT', 'SMTP_USERNAME', 'SMTP_PASSWORD', 'FROM_EMAIL', 'TO_EMAIL']);
    }
    
    foreach ($required as $var) {
        if (empty($_ENV[$var])) {
            exitWithError("Missing required environment variable: $var");
        }
    }
}

function exitWithError(string $message): void
{
    echo "Error: $message\n";
    exit(1);
}

// Client Creation
function createNotionClient(): Client
{
    return new Client((new ClientOptions())->setAuth($_ENV['NOTION_TOKEN']));
}

// DateTime Utilities
function convertToDateTime($dateTime): DateTime
{
    if ($dateTime instanceof DateTimeImmutable) {
        $dt = DateTime::createFromImmutable($dateTime);
    } else {
        $dt = new DateTime($dateTime);
    }
    
    // Ensure we're working in UTC
    $dt->setTimezone(new DateTimeZone('UTC'));
    return $dt;
}

function createUtcDateTime(string $dateString = 'now'): DateTime
{
    return new DateTime($dateString, new DateTimeZone('UTC'));
}

function formatDateTime(DateTime $dateTime): string
{
    return $dateTime->format('Y-m-d H:i:s') . ' UTC';
}

// State Management
function getLastCheckTime(): ?DateTime
{
    if (!file_exists(STATE_FILE)) {
        echo "First run detected\n";
        return null;
    }
    
    $timestamp = trim(file_get_contents(STATE_FILE));
    if (empty($timestamp)) {
        return null;
    }
    
    try {
        $dateTime = createUtcDateTime($timestamp);
        echo "Last check: " . formatDateTime($dateTime) . "\n";
        return $dateTime;
    } catch (Exception $e) {
        echo "Invalid state file, starting fresh\n";
        return null;
    }
}

function saveLastCheckTime(): void
{
    $now = createUtcDateTime();
    $timestamp = $now->format('c'); // ISO 8601 format with timezone
    
    if (file_put_contents(STATE_FILE, $timestamp) === false) {
        echo "Warning: Could not save state\n";
    } else {
        echo "State saved: " . formatDateTime($now) . "\n";
    }
}

// Page Analysis
function getPageTitle($page): string
{
    foreach ($page->getProperties() as $property) {
        if ($property instanceof TitlePropertyValue) {
            $titles = $property->getTitle();
            return !empty($titles) ? $titles[0]->getPlainText() : 'Untitled';
        }
    }
    
    return 'Untitled';
}

function getPageStatus($page, string $statusProperty): ?string
{
    $properties = $page->getProperties();
    
    if (!isset($properties[$statusProperty])) {
        return null;
    }
    
    $property = $properties[$statusProperty];
    
    if ($property instanceof SelectPropertyValue && $property->getSelect()) {
        return $property->getSelect()->getName();
    }
    
    if ($property instanceof StatusPropertyValue && $property->getStatus()) {
        return $property->getStatus()->getName();
    }
    
    return null;
}

function shouldSendNotification($page, string $statusProperty, string $targetStatus): bool
{
    return getPageStatus($page, $statusProperty) === $targetStatus;
}

// Database Operations
function queryUpdatedPages(Client $notion, string $databaseId, ?DateTime $lastCheck): array
{
    $pages = [];
    $cursor = null;
    
    do {
        $pagination = $cursor ? (new PaginationRequest())->setStartCursor($cursor) : null;
        $result = $notion->databases()->query($databaseId, null, $pagination);
        foreach ($result->getResults() as $page) {
            if (isPageUpdated($page, $lastCheck)) {
                $pages[] = $page;
            }
        }
        
        $cursor = $result->isHasMore() ? $result->getNextCursor() : null;
    } while ($cursor);
    
    return $pages;
}

function isPageUpdated($page, ?DateTime $lastCheck): bool
{
    if (!$lastCheck) {
        return true;
    }
    
    $pageTime = convertToDateTime($page->getLastEditedTime());
    return $pageTime > $lastCheck;
}

// Email Operations
function isEmailConfigured(): bool
{
    $emailVars = ['SMTP_HOST', 'SMTP_PORT', 'SMTP_USERNAME', 'SMTP_PASSWORD', 'FROM_EMAIL', 'TO_EMAIL'];
    
    foreach ($emailVars as $var) {
        if (empty($_ENV[$var])) {
            return false;
        }
    }
    
    return true;
}

function sendNotification($page, string $status): bool
{
    if (!isEmailConfigured()) {
        echo "  → Email not configured\n";
        return false;
    }
    
    try {
        $mailer = createMailer();
        $email = createNotificationEmail($page, $status);
        
        $mailer->send($email);
        echo "  → Email sent to " . $_ENV['TO_EMAIL'] . "\n";
        return true;
    } catch (Exception $e) {
        echo "  → Email failed: " . $e->getMessage() . "\n";
        return false;
    }
}

function createMailer(): Mailer
{
    $dsn = sprintf('smtp://%s:%s@%s:%s',
        urlencode($_ENV['SMTP_USERNAME']),
        urlencode($_ENV['SMTP_PASSWORD']),
        $_ENV['SMTP_HOST'],
        $_ENV['SMTP_PORT']
    );
    
    return new Mailer(Transport::fromDsn($dsn));
}

function createNotificationEmail($page, string $status): Email
{
    $title = getPageTitle($page);
    $url = $page->getUrl();
    $time = formatDateTime(convertToDateTime($page->getLastEditedTime()));
    
    $subject = "Task Completed: $title";
    $body = "Task: $title\nStatus: $status\nUpdated: $time\nView: $url";
    
    return (new Email())
        ->from($_ENV['FROM_EMAIL'])
        ->to($_ENV['TO_EMAIL'])
        ->subject($subject)
        ->text($body);
}

function testEmail(): void
{
    echo "Testing email...\n";
    
    try {
        $mailer = createMailer();
        $email = (new Email())
            ->from($_ENV['FROM_EMAIL'])
            ->to($_ENV['TO_EMAIL'])
            ->subject('Notion Monitor Test')
            ->text('Email configuration working!');
        
        $mailer->send($email);
        echo "✅ Test email sent\n";
    } catch (Exception $e) {
        echo "❌ Email test failed: " . $e->getMessage() . "\n";
        exit(1);
    }
}

// Monitoring Logic
function processPages(array $pages, string $statusProperty, string $targetStatus): array
{
    $stats = ['sent' => 0, 'failed' => 0];
    
    foreach ($pages as $page) {
        $title = getPageTitle($page);
        $status = getPageStatus($page, $statusProperty);
        
        if (shouldSendNotification($page, $statusProperty, $targetStatus)) {
            echo "✓ \"$title\" → \"$status\"\n";
            
            if (sendNotification($page, $status)) {
                $stats['sent']++;
            } else {
                $stats['failed']++;
            }
        } else {
            $statusText = $status ?: 'none';
            echo "• \"$title\" → \"$statusText\"\n";
        }
    }
    
    return $stats;
}

function runMonitoring(): void
{
    $statusProperty = $_ENV['STATUS_PROPERTY'] ?? 'Status';
    $targetStatus = $_ENV['TARGET_STATUS'] ?? 'Done';
    
    $notion = createNotionClient();
    $databaseId = $_ENV['NOTION_DATABASE_ID'];
    $lastCheck = getLastCheckTime();
    
    if (!isEmailConfigured()) {
        echo "Warning: Email not configured\n\n";
    }
    
    $timeLabel = $lastCheck ? formatDateTime($lastCheck) : 'beginning';
    echo "Checking for updates since: $timeLabel\n";
    
    $pages = queryUpdatedPages($notion, $databaseId, $lastCheck);
    echo "Found " . count($pages) . " updated pages\n\n";
    
    $stats = processPages($pages, $statusProperty, $targetStatus);
    
    echo "\nComplete: {$stats['sent']} sent";
    if ($stats['failed'] > 0) {
        echo ", {$stats['failed']} failed";
    }
    echo "\n";
    
    saveLastCheckTime();
}

// Main Execution
function main(): void
{
    global $argv;
    
    try {
        echo "Notion Database Monitor\n";
        echo "======================\n\n";
        
        loadEnvironment();
        
        $isTestMode = isset($argv[1]) && $argv[1] === 'test';
        validateEnvironment($isTestMode);
        
        if ($isTestMode) {
            testEmail();
        } else {
            runMonitoring();
        }
    } catch (ApiResponseException $e) {
        exitWithError("Notion API: " . $e->getMessage());
    } catch (Exception $e) {
        exitWithError($e->getMessage());
    }
}

main(); 
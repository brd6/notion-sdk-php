<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use Brd6\NotionSdkPhp\Client as NotionClient;
use Brd6\NotionSdkPhp\ClientOptions;
use Brd6\NotionSdkPhp\Exception\ApiResponseException;
use Brd6\NotionSdkPhp\Resource\Comment;
use Brd6\NotionSdkPhp\Resource\Page;
use Brd6\NotionSdkPhp\Resource\Page\Parent\PageIdParent;
use Brd6\NotionSdkPhp\Resource\Page\PropertyValue\SelectPropertyValue;
use Brd6\NotionSdkPhp\Resource\Page\PropertyValue\StatusPropertyValue;
use Brd6\NotionSdkPhp\Resource\Property\SelectProperty;
use Brd6\NotionSdkPhp\Resource\RichText\Text;
use Dotenv\Dotenv;
use Github\Api\PullRequest;
use Github\Client as GitHubClient;

const STATE_FILE = __DIR__ . '/processed_prs.json';

// Helper Utilities
abstract class NotionHelper
{
    public static function isNotionPageId(?string $notionPageId): bool
    {
        $regex = '/^([a-f0-9]{32}|[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12})$/i';
        return $notionPageId !== null && preg_match($regex, $notionPageId) === 1;
    }

    public static function convertToHyphenatedNotionId(string $notionId): string
    {
        if (strlen($notionId) !== 32 || str_contains($notionId, '-')) {
            return $notionId;
        }

        return substr($notionId, 0, 8).'-'.substr($notionId, 8, 4).'-'.substr($notionId, 12, 4).'-'.substr($notionId, 16, 4).'-'.substr($notionId, 20);
    }

    public static function extractIdFromUrl(string $url): ?string
    {
        $matches = [];
        if (preg_match('/[-\/]([a-f0-9\-]{32,})/m', $url, $matches) !== false && count($matches) > 1) {
            return $matches[1];
        }
        return null;
    }
}

// Environment Management
function loadEnvironment(): void
{
    if (!file_exists(__DIR__ . '/.env')) {
        exitWithError('Missing .env file. Copy .env.example to .env and configure it.');
    }
    
    Dotenv::createImmutable(__DIR__)->load();
}

function validateEnvironment(): void
{
    $required = ['GITHUB_KEY', 'NOTION_KEY', 'GITHUB_REPO_OWNER', 'GITHUB_REPO_NAME'];
    
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
function createNotionClient(): NotionClient
{
    return new NotionClient((new ClientOptions())->setAuth($_ENV['NOTION_KEY']));
}

function createGitHubClient(): GitHubClient
{
    $client = new GitHubClient();
    $client->authenticate($_ENV['GITHUB_KEY'], null, GitHubClient::AUTH_ACCESS_TOKEN);
    return $client;
}

// State Management
function loadProcessedPRs(): array
{
    if (!file_exists(STATE_FILE)) {
        echo "First run detected - no processed PRs found\n";
        return [];
    }
    
    $content = file_get_contents(STATE_FILE);
    if (empty($content)) {
        return [];
    }
    
    try {
        $data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        echo "Loaded " . count($data) . " processed PRs from state file\n";
        return $data;
    } catch (JsonException $e) {
        echo "Warning: Invalid state file, starting fresh: " . $e->getMessage() . "\n";
        return [];
    }
}

function saveProcessedPR(string $repository, int $prNumber, string $pageId): void
{
    $processedPRs = loadProcessedPRs();
    $key = createPRKey($repository, $prNumber);
    
    $processedPRs[$key] = [
        'repository' => $repository,
        'pr_number' => $prNumber,
        'page_id' => $pageId,
        'processed_at' => (new DateTime('now', new DateTimeZone('UTC')))->format('c'),
    ];
    
    if (file_put_contents(STATE_FILE, json_encode($processedPRs, JSON_PRETTY_PRINT)) === false) {
        echo "Warning: Could not save state\n";
    }
}

function createPRKey(string $repository, int $prNumber): string
{
    return "{$repository}#{$prNumber}";
}

function hasBeenProcessed(string $repository, int $prNumber): bool
{
    $processedPRs = loadProcessedPRs();
    $key = createPRKey($repository, $prNumber);
    return isset($processedPRs[$key]);
}

function getProcessedPRInfo(string $repository, int $prNumber): ?array
{
    $processedPRs = loadProcessedPRs();
    $key = createPRKey($repository, $prNumber);
    return $processedPRs[$key] ?? null;
}

// URL Processing
function extractNotionPageId(string $text): ?string
{
    $pageId = NotionHelper::extractIdFromUrl($text);
    
    if (!$pageId) {
        return null;
    }
    
    $normalizedId = NotionHelper::convertToHyphenatedNotionId($pageId);
    
    return NotionHelper::isNotionPageId($normalizedId) ? $normalizedId : null;
}

// GitHub Operations
function fetchClosedPRs(GitHubClient $github, string $owner, string $repo): array
{
    try {
        /** @var PullRequest $pullRequestApi */
        $pullRequestApi = $github->api('pull_request');
        return $pullRequestApi->all($owner, $repo, ['state' => 'closed']);
    } catch (Exception $e) {
        exitWithError("Failed to fetch PRs: " . $e->getMessage());
        return []; // Never reached, but satisfies linter
    }
}

// Notion Operations
function getBotUserId(NotionClient $notion): ?string
{
    try {
        $user = $notion->users()->me();
        return $user->getId();
    } catch (Exception $e) {
        echo "Warning: Could not get bot user ID: " . $e->getMessage() . "\n";
        return null;
    }
}

function determineNotionStatus(string $prState, bool $merged): string
{
    return $merged ? 'Merged' : 'Closed';
}

function updateNotionPageStatus(NotionClient $notion, string $pageId, string $status): bool
{
    if (!shouldUpdateStatus()) {
        echo "  → Status update disabled\n";
        return true;
    }
    
    $statusProperty = $_ENV['STATUS_PROPERTY_NAME'] ?? 'Status';
    
    try {
        $page = $notion->pages()->retrieve($pageId);
        $properties = $page->getProperties();
        
        if (!isset($properties[$statusProperty])) {
            echo "  → Status property '$statusProperty' not found\n";
            return false;
        }
        
        $statusValue = createStatusPropertyValue($properties[$statusProperty], $status);
        if (!$statusValue) {
            echo "  → Unsupported status property type\n";
            return false;
        }
        
        $updatePage = createPageUpdateRequest($pageId, $statusProperty, $statusValue);
        $notion->pages()->update($updatePage);
        
        echo "  → Status updated to \"$status\"\n";
        return true;
    } catch (ApiResponseException $e) {
        echo "  → Failed to update status: " . $e->getMessage() . "\n";
        return false;
    }
}

function shouldUpdateStatus(): bool
{
    return $_ENV['UPDATE_STATUS_IN_NOTION_DB'] === 'true';
}

function createStatusPropertyValue($property, string $status)
{
    $selectProperty = (new SelectProperty())->setName($status);
    
    if ($property instanceof SelectPropertyValue) {
        return (new SelectPropertyValue())->setSelect($selectProperty);
    }
    
    if ($property instanceof StatusPropertyValue) {
        return (new StatusPropertyValue())->setStatus($selectProperty);
    }
    
    return null;
}

function createPageUpdateRequest(string $pageId, string $propertyName, $propertyValue): Page
{
    $page = new Page();
    $page->setId($pageId);
    $page->setProperties([$propertyName => $propertyValue]);
    
    return $page;
}

function hasExistingComment(NotionClient $notion, string $pageId, string $botUserId, int $prNumber): bool
{
    try {
        $comments = $notion->comments()->retrieve($pageId);
        $searchText = "PR #$prNumber";
        
        foreach ($comments->getResults() as $comment) {
            $authorId = $comment->getCreatedBy()->getId();
            if ($authorId === $botUserId) {
                $commentText = '';
                foreach ($comment->getRichText() as $richText) {
                    $commentText .= $richText->getPlainText();
                }
                
                if (str_contains($commentText, $searchText)) {
                    return true;
                }
            }
        }
        
        return false;
    } catch (Exception $e) {
        echo "  → Warning: Could not check existing comments: " . $e->getMessage() . "\n";
        return false;
    }
}

function addCommentToNotionPage(NotionClient $notion, string $pageId, string $prUrl, int $prNumber, string $prTitle, string $status): bool
{
    try {
        $comment = new Comment();
        $comment->setParent((new PageIdParent())->setPageId($pageId));
        
        $commentText = "🔗 PR #$prNumber \"$prTitle\" has been $status\n\nView PR: $prUrl";
        $richText = Text::fromContent($commentText);
        $comment->setRichText([$richText]);
        
        $notion->comments()->create($comment);
        echo "  → Comment added: PR #$prNumber marked as $status\n";
        return true;
    } catch (Exception $e) {
        echo "  → Failed to add comment: " . $e->getMessage() . "\n";
        return false;
    }
}

// Processing Logic
function processPullRequest(NotionClient $notion, array $pr, string $repository, ?string $botUserId): bool
{
    $prData = extractPRData($pr);
    echo "✓ PR #{$prData['number']}: \"{$prData['title']}\"\n";
    
    // Check if already processed
    if (hasBeenProcessed($repository, $prData['number'])) {
        $processedInfo = getProcessedPRInfo($repository, $prData['number']);
        $processedAt = $processedInfo['processed_at'] ?? 'unknown';
        echo "  → Already processed at $processedAt, skipping\n";
        return false;
    }
    
    $pageId = extractNotionPageId($prData['body']);
    if (!$pageId) {
        echo "  → No Notion URL found, skipping\n";
        return false;
    }
    
    echo "  → Notion page: $pageId\n";
    
    $success = updateNotionTask($notion, $pageId, $prData, $botUserId);
    
    if ($success) {
        saveProcessedPR($repository, $prData['number'], $pageId);
        echo "  → Marked as processed\n";
    }
    
    return $success;
}

function extractPRData(array $pr): array
{
    return [
        'number' => $pr['number'],
        'title' => $pr['title'],
        'body' => $pr['body'] ?? '',
        'url' => $pr['html_url'],
        'state' => $pr['state'],
        'merged' => $pr['merged'] ?? false,
    ];
}

function updateNotionTask(NotionClient $notion, string $pageId, array $prData, ?string $botUserId): bool
{
    $status = determineNotionStatus($prData['state'], $prData['merged']);
    
    // Check if we've already commented on this PR to avoid duplicates
    if ($botUserId && hasExistingComment($notion, $pageId, $botUserId, $prData['number'])) {
        echo "  → Comment already exists for PR #{$prData['number']}, skipping\n";
        return false;
    }
    
    $statusUpdated = updateNotionPageStatus($notion, $pageId, $status);
    $commentAdded = addCommentToNotionPage($notion, $pageId, $prData['url'], $prData['number'], $prData['title'], $status);
    
    return $statusUpdated || $commentAdded;
}

// Main Sync Process
function executeSync(): void
{
    $repoConfig = getRepositoryConfig();
    $clients = createClients();
    $repository = "{$repoConfig['owner']}/{$repoConfig['repo']}";
    
    echo "Fetching closed PRs from $repository...\n";
    $prs = fetchClosedPRs($clients['github'], $repoConfig['owner'], $repoConfig['repo']);
    echo "Found " . count($prs) . " closed PRs to analyze\n\n";
    
    $botUserId = getBotUserId($clients['notion']);
    $updatedCount = 0;
    
    foreach ($prs as $pr) {
        if (processPullRequest($clients['notion'], $pr, $repository, $botUserId)) {
            $updatedCount++;
        }
        echo "\n";
    }
    
    echo "Sync complete: $updatedCount tasks updated\n";
}

function getRepositoryConfig(): array
{
    return [
        'owner' => $_ENV['GITHUB_REPO_OWNER'],
        'repo' => $_ENV['GITHUB_REPO_NAME'],
    ];
}

function createClients(): array
{
    return [
        'github' => createGitHubClient(),
        'notion' => createNotionClient(),
    ];
}

// Application Entry Point
function main(): void
{
    try {
        echo "GitHub PR to Notion Sync\n";
        echo "=======================\n\n";
        
        loadEnvironment();
        validateEnvironment();
        executeSync();
    } catch (Exception $e) {
        exitWithError($e->getMessage());
    }
}

main(); 
<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use Brd6\NotionSdkPhp\Client;
use Brd6\NotionSdkPhp\ClientOptions;
use Brd6\NotionSdkPhp\Exception\ApiResponseException;
use Brd6\NotionSdkPhp\Resource\Block\AbstractBlock;
use Brd6\NotionSdkPhp\Resource\Block\BulletedListItemBlock;
use Brd6\NotionSdkPhp\Resource\Block\CalloutBlock;
use Brd6\NotionSdkPhp\Resource\Block\CodeBlock;
use Brd6\NotionSdkPhp\Resource\Block\Heading1Block;
use Brd6\NotionSdkPhp\Resource\Block\Heading2Block;
use Brd6\NotionSdkPhp\Resource\Block\Heading3Block;
use Brd6\NotionSdkPhp\Resource\Block\NumberedListItemBlock;
use Brd6\NotionSdkPhp\Resource\Block\ParagraphBlock;
use Brd6\NotionSdkPhp\Resource\Block\QuoteBlock;
use Brd6\NotionSdkPhp\Resource\Block\ToDoBlock;
use Brd6\NotionSdkPhp\Resource\Block\ToggleBlock;
use Brd6\NotionSdkPhp\Resource\Pagination\PaginationRequest;
use Brd6\NotionSdkPhp\Resource\RichText\AbstractRichText;
use Dotenv\Dotenv;

function loadEnvironmentVariables(): void
{
    if (!file_exists(__DIR__ . '/.env')) {
        echo "Error: .env file not found!\n";
        echo "Please create a .env file by copying env.example:\n";
        echo "  cp .env.example .env\n";
        echo "Then add your Notion token and page ID to the .env file.\n";
        exit(1);
    }
    
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}

function validateRequiredEnvironmentVariables(): void
{
    $requiredVars = ['NOTION_TOKEN', 'NOTION_PAGE_ID'];
    
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

function extractTextFromRichTextArray(array $richTextArray): string
{
    $text = '';
    
    foreach ($richTextArray as $richText) {
        if ($richText instanceof AbstractRichText) {
            $text .= $richText->getPlainText();
        }
    }
    
    return $text;
}

function extractTextFromBlock(AbstractBlock $block): string
{
    $text = '';
    
    if ($block instanceof ParagraphBlock) {
        $paragraphProperty = $block->getParagraph();
        if ($paragraphProperty !== null) {
            $text .= extractTextFromRichTextArray($paragraphProperty->getRichText());
        }
    } elseif ($block instanceof Heading1Block) {
        $headingProperty = $block->getHeading1();
        if ($headingProperty !== null) {
            $text .= extractTextFromRichTextArray($headingProperty->getRichText());
        }
    } elseif ($block instanceof Heading2Block) {
        $headingProperty = $block->getHeading2();
        if ($headingProperty !== null) {
            $text .= extractTextFromRichTextArray($headingProperty->getRichText());
        }
    } elseif ($block instanceof Heading3Block) {
        $headingProperty = $block->getHeading3();
        if ($headingProperty !== null) {
            $text .= extractTextFromRichTextArray($headingProperty->getRichText());
        }
    } elseif ($block instanceof BulletedListItemBlock) {
        $listItemProperty = $block->getBulletedListItem();
        if ($listItemProperty !== null) {
            $text .= extractTextFromRichTextArray($listItemProperty->getRichText());
        }
    } elseif ($block instanceof NumberedListItemBlock) {
        $listItemProperty = $block->getNumberedListItem();
        if ($listItemProperty !== null) {
            $text .= extractTextFromRichTextArray($listItemProperty->getRichText());
        }
    } elseif ($block instanceof ToggleBlock) {
        $toggleProperty = $block->getToggle();
        if ($toggleProperty !== null) {
            $text .= extractTextFromRichTextArray($toggleProperty->getRichText());
        }
    } elseif ($block instanceof QuoteBlock) {
        $quoteProperty = $block->getQuote();
        if ($quoteProperty !== null) {
            $text .= extractTextFromRichTextArray($quoteProperty->getRichText());
        }
    } elseif ($block instanceof CalloutBlock) {
        $calloutProperty = $block->getCallout();
        if ($calloutProperty !== null) {
            $text .= extractTextFromRichTextArray($calloutProperty->getRichText());
        }
    } elseif ($block instanceof CodeBlock) {
        $codeProperty = $block->getCode();
        if ($codeProperty !== null) {
            $text .= extractTextFromRichTextArray($codeProperty->getRichText());
        }
    } elseif ($block instanceof ToDoBlock) {
        $todoProperty = $block->getToDo();
        if ($todoProperty !== null) {
            $text .= extractTextFromRichTextArray($todoProperty->getRichText());
        }
    }
    
    if (!empty($text)) {
        $text .= "\n";
    }
    
    return $text;
}

function extractTextFromBlocks(Client $notion, string $blockId): string
{
    $allText = '';
    $startCursor = null;

    do {
        $paginationRequest = new PaginationRequest();
        if ($startCursor !== null) {
            $paginationRequest->setStartCursor($startCursor);
        }

        $result = $notion->blocks()->children()->list($blockId, $paginationRequest);
        $blocks = $result->getResults();

        foreach ($blocks as $block) {
            if (!($block instanceof AbstractBlock)) {
                continue;
            }

            $blockText = extractTextFromBlock($block);
            $allText .= $blockText;

            if ($block->isHasChildren()) {
                $childrenText = extractTextFromBlocks($notion, $block->getId());
                $allText .= $childrenText;
            }
        }

        $hasMore = $result->isHasMore();
        $startCursor = $hasMore ? $result->getNextCursor() : null;

    } while ($hasMore);

    return $allText;
}

function main(): void
{
    try {
        echo "Notion SDK PHP - Text Extraction Example\n";
        echo "========================================\n\n";
        
        loadEnvironmentVariables();
        validateRequiredEnvironmentVariables();
        
        $notion = createNotionClient();
        $pageId = $_ENV['NOTION_PAGE_ID'];
        
        echo "Extracting text from page: $pageId\n";
        echo "Processing blocks recursively...\n\n";
        
        $extractedText = extractTextFromBlocks($notion, $pageId);
        
        if (empty(trim($extractedText))) {
            echo "No text content found on this page.\n";
        } else {
            echo "Extracted Text:\n";
            echo "===============\n\n";
            echo $extractedText;
        }
        
        echo "\nText extraction completed successfully!\n";
        
    } catch (ApiResponseException $e) {
        echo "Notion API Error: " . $e->getMessage() . "\n";
        echo "Please check your token and page ID, and ensure the page is shared with your integration.\n";
        exit(1);
    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
        exit(1);
    }
}

main(); 
# Parse Text from Any Block Type

This example demonstrates how to use the `notion-sdk-php` to recursively fetch all blocks on a page and extract their complete text content. This is useful for tasks like indexing page content for search.

The script handles pagination and nested blocks (like toggles or columns) to ensure all text is extracted.

## Prerequisites

- PHP 7.4 or higher
- Composer
- Notion Integration Token
- Notion Page shared with your integration

## Setup

### 1. Install Dependencies

Navigate to this directory and install the required packages using Composer:

```bash
cd examples/02-parse-text-from-any-block
composer install
```

### 2. Environment Configuration

Create a `.env` file by copying the example file:

```bash
cp .env.example .env
```

Open the `.env` file and add your Notion integration token and page ID:

```dotenv
NOTION_TOKEN="secret_..."
NOTION_PAGE_ID="..."
```

## Usage

Execute the script from your terminal:

```bash
php index.php
```

The script will:
- Connect to your Notion workspace
- Recursively fetch all blocks from the specified page
- Extract text content from each block type
- Handle nested blocks (toggles, columns, etc.)
- Output the complete concatenated text content 
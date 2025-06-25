# Introduction to the Notion API with PHP

This example demonstrates the basics of using the `notion-sdk-php` library. It's a simple command-line script that:

1. Connects to the Notion API
2. Queries an existing database and lists its pages
3. Adds a new page to that database

## Prerequisites

- PHP 7.4 or higher
- Composer
- Notion Integration Token
- Notion Database shared with your integration

## Setup

### 1. Install Dependencies

Navigate to this directory and install the required packages using Composer:

```bash
cd examples/01-intro-to-notion-api
composer install
```

### 2. Environment Configuration

Create a `.env` file by copying the example file:

```bash
cp .env.example .env
```

Open the `.env` file and add your Notion integration token and database ID:

```dotenv
NOTION_TOKEN="secret_..."
NOTION_DATABASE_ID="..."
```

## Usage

Execute the script from your terminal:

```bash
php index.php
```

The script will:
- Connect to your Notion workspace
- List existing pages in the specified database
- Create a new page with a timestamped title
- Display the URL of the newly created page 
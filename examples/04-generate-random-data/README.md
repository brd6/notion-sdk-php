# Generate Random Data for Notion Database

This example demonstrates how to populate a Notion database with randomly generated sample data using the `notion-sdk-php`. It's particularly useful for testing, development, and demonstrating various Notion property types.

The script automatically detects the database schema and generates appropriate random data for each property type.

## Prerequisites

- PHP 7.4 or higher
- Composer
- Notion Integration Token
- Notion Database shared with your integration (can be empty or with existing schema)

## Setup

### 1. Install Dependencies

Navigate to this directory and install the required packages using Composer:

```bash
cd examples/04-generate-random-data
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

Generate 10 random entries (default):

```bash
php index.php
```

Generate a specific number of entries:

```bash
php index.php 50
```

Or use the composer script:

```bash
composer generate
```

## Supported Property Types

The script can generate data for the following Notion property types:

- **Title**: Company names, project titles
- **Rich Text**: Descriptions, comments
- **Number**: Quantities, scores, prices
- **Select**: Status, priority, category
- **Multi-select**: Tags, skills, departments
- **Date**: Deadlines, created dates
- **Checkbox**: Boolean values
- **Email**: Fake email addresses
- **Phone**: Fake phone numbers
- **URL**: Fake website URLs

## Features

- Automatic database schema detection
- Progress updates during generation
- Batch processing for better performance
- Error handling with informative messages
- Realistic, contextual fake data using Faker library
- Support for any database structure

## Example Output

```
Notion SDK PHP - Random Data Generator
=====================================

Analyzing database schema...
Found 5 properties: Name (title), Email (email), Status (select), Created (date), Active (checkbox)

Generating 10 random entries...
Progress: [██████████] 10/10 (100%)

Successfully created 10 entries in your Notion database! 
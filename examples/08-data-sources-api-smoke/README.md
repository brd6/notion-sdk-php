# Data Sources API Integration Example

This example runs Notion API integration checks for the `2025-09-03` API version using the SDK. It validates the main data-source flow directly against your workspace:

- retrieve database
- retrieve data source
- query data source
- search data sources
- optional create + update data source (write mode)

It is intended for live API validation and fixture discovery without touching the test fixtures in this repository.

## Setup

Install dependencies:

```bash
cd examples/08-data-sources-api-smoke
composer install
```

Create your `.env`:

```bash
cp .env.example .env
```

Required values:

```dotenv
NOTION_TOKEN="secret_..."
NOTION_DATABASE_ID="..."
```

Optional values:

```dotenv
NOTION_DATA_SOURCE_ID="..." # optional override
NOTION_RUN_WRITES=0         # set to 1 for create/update calls
```

## Usage

Read-only checks:

```bash
php index.php
```

With write checks enabled:

```bash
NOTION_RUN_WRITES=1 php index.php
```

Write mode creates a temporary data source and then updates it (`in_trash=true`) to keep cleanup simple.

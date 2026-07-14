# File Uploads API Integration Example

This example runs Notion File Upload API integration checks using the SDK's `fileUploads()` endpoint. It validates the full upload flow directly against your workspace:

- upload a file in one call — create + multipart send (`fileUploads()->upload()`)
- retrieve the file upload and verify its status is `uploaded` (`fileUploads()->retrieve()`)
- attach the uploaded image to a page as an image block (`blocks()->children()->append()`)
- list recent uploads (`fileUploads()->list()`)

It is intended for live API validation and fixture discovery without touching the test fixtures in this repository.

## Setup

Install dependencies:

```bash
cd examples/09-file-uploads-api-smoke
composer install
```

Create your `.env`:

```bash
cp .env.example .env
```

Required values:

```dotenv
NOTION_TOKEN="secret_..."
NOTION_PAGE_ID="..."
```

The page must be shared with your integration.

## Usage

```bash
php index.php
```

The script uploads a small PNG embedded in the example (no file on disk needed) and appends it to the configured page as an image block. Expected output:

```text
Uploading sample.png...
File uploaded: <id> (status: uploaded)
File upload verified (status: uploaded)
Attaching the uploaded image to the page...
Image block created: <block-id>
Listing recent uploads...
Uploads found: <count>
Done. Check the page in Notion to see the uploaded image.
```

## Notes

- Uploaded files count against your workspace storage. Free workspaces are limited to 5 MiB per file, and multi-part uploads (`FileUploadRequest::MODE_MULTI_PART`) require a paid plan.

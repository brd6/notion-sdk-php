# File Uploads API Integration Example

This example runs Notion File Upload API integration checks using the SDK's raw request support (per-request headers and raw bodies). It validates the full upload flow directly against your workspace:

- create a file upload (`POST /v1/file_uploads`)
- send the file contents as `multipart/form-data` (`POST /v1/file_uploads/{id}/send`)
- retrieve the file upload and verify its status is `uploaded`
- attach the uploaded image to a page as an image block

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
Creating file upload...
File upload created: <id> (status: pending)
Sending file contents as multipart/form-data...
File contents sent (status: uploaded)
File upload verified (status: uploaded)
Attaching the uploaded image to the page...
Image block created: <block-id>
Done. Check the page in Notion to see the uploaded image.
```

## Notes

- The multipart request is built with `php-http/multipart-stream-builder`, which the SDK already depends on, and sent through `Client::request()` using `RequestParameters::setHeaders()` and `RequestParameters::setRawBody()`.
- Uploaded files count against your workspace storage. Free workspaces are limited to 5 MiB per file.

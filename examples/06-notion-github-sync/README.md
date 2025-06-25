# GitHub PR to Notion Task Sync

This example demonstrates a practical automation that updates Notion tasks when a linked GitHub Pull Request is closed or merged.

The script scans for closed PRs in a repository. If a PR's description contains a link to a Notion page, the script will:
1. Check if it has already processed this PR to avoid duplicates
2. Update a "Status" property on the Notion page to "Merged" or "Closed" (if enabled)
3. Add a comment to the Notion page with PR details and link back to GitHub
4. Track the processed PR to prevent future duplicate processing

## Prerequisites

- PHP 7.4 or higher
- Composer
- GitHub Personal Access Token with `repo` scope
- Notion Integration Token
- Notion Database with a "Status" property (Select or Status type)

## Setup

### 1. Install Dependencies

Navigate to this directory and install the required packages using Composer:

```bash
cd examples/06-notion-github-sync
composer install
```

### 2. Environment Configuration

Create a `.env` file by copying the example file:

```bash
cp .env.example .env
```

Configure your settings in the `.env` file:

```dotenv
# Your GitHub Personal Access Token
GITHUB_KEY="ghp_..."

# Your Notion integration token
NOTION_KEY="secret_..."

# The owner of the GitHub repository to scan
GITHUB_REPO_OWNER="your-username"

# The name of the GitHub repository to scan
GITHUB_REPO_NAME="your-repo"

# Set to true or false to control whether the script updates the status property
UPDATE_STATUS_IN_NOTION_DB=true

# The exact name of the "Status" property in your Notion database
STATUS_PROPERTY_NAME="Status"
```

### 3. GitHub Token Setup

1. Go to GitHub Settings → Developer settings → Personal access tokens
2. Generate a new token with `repo` scope
3. Copy the token to your `.env` file

### 4. Notion Setup

1. Create a Notion integration at https://www.notion.so/my-integrations
2. Copy the integration token to your `.env` file
3. Share your database with the integration
4. Ensure your database has a "Status" property (Select or Status type)

## Usage

### Manual Sync

Run the sync script manually:

```bash
php index.php
```

Or use the composer script:

```bash
composer sync
```

### Automated Sync

Set up a cron job to run the sync automatically:

```bash
# Edit crontab
crontab -e

# Add this line to run every 15 minutes
*/15 * * * * cd /path/to/examples/06-notion-github-sync && php index.php >> /tmp/github-notion-sync.log 2>&1
```

## Workflow

1. **Create a Notion Task**: Add a task to your Notion database
2. **Create a GitHub PR**: Create a pull request to address the task
3. **Link them**: Paste the Notion page URL in the PR description
4. **Automatic Update**: When the PR is merged/closed, the script updates the Notion task

### Example PR Description

```markdown
This PR implements user authentication as requested.

Related Notion task: https://www.notion.so/your-workspace/task-name-123456789

- Added login form
- Implemented JWT authentication
- Added password hashing
```

## State Management

The script maintains a `processed_prs.json` file to track which PRs have been processed. This ensures:

- **No duplicate processing**: Each PR is only processed once, even if you run the script multiple times
- **Persistent state**: The script remembers what it has done across runs
- **Audit trail**: You can see when each PR was processed and which Notion page it affected

The state file contains entries like:
```json
{
  "owner/repo#123": {
    "repository": "owner/repo",
    "pr_number": 123,
    "page_id": "12345678-1234-1234-1234-123456789012",
    "processed_at": "2025-06-25T16:30:00+00:00"
  }
}
```

To reset the state and reprocess all PRs, simply delete the `processed_prs.json` file.

## Features

- **One-Way Sync**: Updates Notion based on GitHub PR status
- **Idempotent**: Won't create duplicate updates if run multiple times
- **State Persistence**: Tracks processed PRs in `processed_prs.json` to prevent duplicates
- **Smart Parsing**: Extracts Notion page IDs from various URL formats
- **Status Mapping**: Maps PR states to appropriate Notion status values
- **Comment Links**: Adds comments to Notion pages with PR details and links back to GitHub
- **Duplicate Prevention**: Checks existing comments to avoid duplicate notifications from the same PR
- **Error Handling**: Graceful failure handling with detailed logging
- **Configurable**: Control status updates and property names via environment

## Supported Notion URL Formats

The script can parse Notion page IDs from these URL formats:

- `https://www.notion.so/workspace/Page-Title-123456789`
- `https://notion.so/Page-Title-123456789`
- `https://www.notion.so/123456789`
- And other variations

## Status Mapping

- **Merged PR** → "Merged" status in Notion
- **Closed PR** → "Closed" status in Notion

## Example Output

```
GitHub PR to Notion Sync
=======================

Loaded 2 processed PRs from state file
Fetching closed PRs from your-username/your-repo...
Found 5 closed PRs to analyze

✓ PR #123: "Add user authentication"
  → Notion page: 12345678-1234-1234-1234-123456789abc
  → Status updated to "Merged"
  → Comment added: PR #123 marked as Merged
  → Marked as processed

• PR #122: "Update README"
  → No Notion link found, skipping

✓ PR #121: "Fix login bug"
  → Already processed at 2025-06-25T15:30:00+00:00, skipping

Sync complete: 1 task updated
```

## Troubleshooting

### GitHub Issues

- **Authentication Failed**: Verify your GitHub token has `repo` scope
- **Repository Not Found**: Check the repository owner and name
- **Rate Limiting**: The script handles rate limits automatically

### Notion Issues

- **Invalid Token**: Verify your integration token is correct
- **Page Not Found**: Ensure the page is shared with your integration
- **Property Not Found**: Check that the STATUS_PROPERTY_NAME matches exactly

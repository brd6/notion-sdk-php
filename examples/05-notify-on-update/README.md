# Database Update Notifications

This example demonstrates how to monitor a Notion database for changes and send email notifications when specific conditions are met. It's designed to run as a scheduled task (cron job) and maintains state between runs to avoid duplicate notifications.

## Prerequisites

- PHP 7.4 or higher
- Composer
- Notion Integration Token
- Notion Database with a "Status" property (or similar)
- SMTP email configuration (Gmail, SendGrid, etc.)

## Setup

### 1. Install Dependencies

Navigate to this directory and install the required packages using Composer:

```bash
cd examples/05-notify-on-update
composer install
```

### 2. Environment Configuration

Create a `.env` file by copying the example file:

```bash
cp .env.example .env
```

Configure your settings in the `.env` file:

```dotenv
# Notion Configuration (Required)
NOTION_TOKEN="secret_..."
NOTION_DATABASE_ID="..."

# Email Configuration (Optional - for notifications)
SMTP_HOST="smtp.gmail.com"
SMTP_PORT="587"
SMTP_USERNAME="your-email@gmail.com"
SMTP_PASSWORD="your-app-password"
FROM_EMAIL="your-email@gmail.com"
TO_EMAIL="recipient@example.com"

# Notification Settings
STATUS_PROPERTY="Status"
TARGET_STATUS="Done"
CHECK_INTERVAL_MINUTES=15
```

**Note:** Email configuration is optional. The monitoring will work without email settings, but no notifications will be sent. This is useful for testing or when you only want to see the console output.

## Usage

### Test Email Configuration

Before setting up monitoring, test your email configuration:

```bash
php index.php test
```

Or use the composer script:

```bash
composer test-email
```

### Run Manual Check

Test the monitoring functionality manually:

```bash
php index.php
```

Or use the composer script:

```bash
composer monitor
```

### Run Without Email (Monitoring Only)

You can run the monitoring without email configuration to see console output only:

```bash
# Remove or comment out email settings in .env
php index.php
```

The script will warn that email is not configured but will continue monitoring and show status changes in the console.

### Set Up Automated Monitoring

Add to your crontab to run every 15 minutes:

```bash
# Edit crontab
crontab -e

# Add this line (adjust path to your installation)
*/15 * * * * cd /path/to/examples/05-notify-on-update && php index.php >> /tmp/notion-monitor.log 2>&1
```

### Reset State (for testing)

To reset the monitoring state and receive notifications for existing items:

```bash
rm last_check.txt
```

## Features

- **Status Change Monitoring**: Tracks when items change to specified status
- **State Persistence**: Maintains state between runs to prevent duplicate notifications
- **Professional Email Templates**: Sends formatted HTML emails with page links
- **Configurable Criteria**: Customize which changes trigger notifications
- **Multiple Email Providers**: Support for Gmail, SendGrid, and custom SMTP
- **Test Mode**: Validate email configuration before deploying
- **Error Handling**: Graceful failure handling for production use
- **Logging**: Detailed output for monitoring and debugging  
- **UTC Timestamps**: All time handling uses UTC for consistent global operation

## Email Provider Configuration

### Gmail

```dotenv
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USERNAME=your-email@gmail.com
SMTP_PASSWORD=your-16-character-app-password
```

### SendGrid

```dotenv
SMTP_HOST=smtp.sendgrid.net
SMTP_PORT=587
SMTP_USERNAME=apikey
SMTP_PASSWORD=your-sendgrid-api-key
```

### Custom SMTP

```dotenv
SMTP_HOST=mail.yourprovider.com
SMTP_PORT=587
SMTP_USERNAME=your-username
SMTP_PASSWORD=your-password
```

## Notification Criteria

The script monitors for status changes by default. You can customize the monitoring criteria:

- **STATUS_PROPERTY**: Name of the property to monitor (default: "Status")
- **TARGET_STATUS**: Status value that triggers notifications (default: "Done")
- **CHECK_INTERVAL_MINUTES**: How often to check for changes (for reference only)

## Troubleshooting

### Email Issues

- **Authentication Failed**: Check your SMTP credentials and app password
- **Connection Timeout**: Verify SMTP host and port settings
- **Messages Not Delivered**: Check spam folder and sender verification

### Notion API Issues

- **Invalid Token**: Verify your integration token is correct
- **Database Not Found**: Ensure the database is shared with your integration
- **Property Not Found**: Check that the STATUS_PROPERTY exists in your database

### State Issues

- **Duplicate Notifications**: Ensure only one instance runs at a time
- **Missing Notifications**: Check that the last_check.txt file is writable
- **Reset State**: Delete last_check.txt to start fresh monitoring

### Timezone Issues

- **Timestamps**: All timestamps are handled in UTC for global consistency
- **State File**: Contains ISO 8601 formatted timestamps with UTC timezone
- **Comparisons**: Page edit times and check times are compared in UTC 
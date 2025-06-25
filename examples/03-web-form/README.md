# Web Form with Notion Integration

This example demonstrates how to create a simple web application that collects user input through an HTML form and submits it to a Notion database using the `notion-sdk-php`.

The application uses the Slim Framework for routing and Twig for templating, providing a complete example of integrating Notion into a web context.

## Prerequisites

- PHP 7.4 or higher with built-in web server support
- Composer
- Notion Integration Token
- Notion Database with the following properties:
  - **Name** (Title)
  - **Email** (Email) 
  - **Message** (Text)

## Setup

### 1. Install Dependencies

Navigate to this directory and install the required packages using Composer:

```bash
cd examples/03-web-form
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

Start the built-in PHP web server:

```bash
composer serve
```

Open your browser and navigate to `http://localhost:8000`. You'll see a form where you can enter:

- **Name**: Your full name
- **Email**: Your email address  
- **Message**: Any message or feedback

When you submit the form, a new page will be created in your Notion database with the provided information.

## Project Structure

- `index.php` - Application entry point and routing
- `FormHandler.php` - Business logic for Notion integration
- `templates/` - Twig templates for the form and success page
  - `form.html` - Main form template
  - `success.html` - Success confirmation page

## Features

- Clean, responsive form design
- Input validation with error messages
- Success confirmation with link to created page
- Graceful error handling for API failures
- Automatic property detection in Notion database 
# Example: OAuth 2.0 Flow with Notion API

This example demonstrates how to implement OAuth 2.0 authorization flow for Notion integrations using the `notion-sdk-php` library and the popular `league/oauth2-client` package.

## Overview

This web application showcases:
- **OAuth 2.0 Flow**: Complete authorization code flow with Notion
- **Custom OAuth Provider**: Custom implementation for Notion's OAuth endpoints
- **Session Management**: Secure token storage and user session handling
- **Workspace Dashboard**: Display workspace information and accessible content
- **Modern UI**: Clean, responsive interface with error handling

## Prerequisites

Before running this example, you need:

1. **PHP 8.1 or higher** with session support
2. **Composer** for dependency management
3. **A Notion OAuth integration** set up in your Notion workspace

### Setting Up Notion OAuth Integration

1. **Go to Notion Integrations**: Visit [https://www.notion.so/my-integrations](https://www.notion.so/my-integrations)

2. **Create New Integration**:
   - Click "New integration"
   - Choose "Public integration"
   - Fill in the basic information:
     - Name: "OAuth Example App" (or your preferred name)
     - Description: "Testing OAuth flow with PHP SDK"
     - Website: `http://localhost:8000` (for development)

3. **Configure OAuth Settings**:
   - **Redirect URLs**: Add `http://localhost:8000/callback`
   - **Capabilities**: Select the permissions your app needs:
     - Read content
     - Update content (optional)
     - Insert content (optional)

4. **Get Your Credentials**:
   - Copy the **Client ID** (public identifier)
   - Copy the **Client Secret** (keep this secure!)

## Installation

1. **Navigate to the example directory**:
   ```bash
   cd examples/07-oauth-flow
   ```

2. **Install dependencies**:
   ```bash
   composer install
   ```

3. **Set up environment variables**:
   Create a `.env` file in the example directory:
   ```bash
   # Copy the example file
   cp .env.example .env
   ```

   Edit the `.env` file with your Notion integration credentials:
   ```env
   # Notion OAuth Configuration
   NOTION_CLIENT_ID=your_actual_client_id_here  
   NOTION_CLIENT_SECRET=your_actual_client_secret_here
   NOTION_REDIRECT_URI=http://localhost:8000/callback
   ```

## Running the Application

1. **Start the PHP development server**:
   ```bash
   composer start
   # Or manually:
   # php -S localhost:8000 -t . index.php
   ```

2. **Open your browser** and navigate to:
   ```
   http://localhost:8000
   ```

3. **Test the OAuth flow**:
   - Click "Connect with Notion"
   - Authorize the application in Notion
   - You'll be redirected back to the dashboard

## How It Works

### OAuth 2.0 Flow

1. **Authorization Request**: User clicks "Connect with Notion"
   - App redirects to Notion's authorization server
   - Includes client ID, redirect URI, and state parameter

2. **User Authorization**: User logs into Notion and grants permissions
   - Notion shows consent screen with requested permissions
   - User approves or denies the request

3. **Authorization Code**: Notion redirects back to your app
   - Includes authorization code and state parameter
   - App verifies state parameter for security

4. **Token Exchange**: App exchanges code for access token
   - Makes server-to-server request to Notion's token endpoint
   - Receives access token and workspace information

5. **API Access**: App can now make authenticated requests
   - Uses access token to call Notion API
   - Displays workspace info and accessible content

### Code Structure

```
src/
├── NotionProvider.php     # Custom OAuth 2.0 provider for Notion
└── NotionResourceOwner.php # Handles user/workspace data from token

templates/
├── login.html            # Login page with OAuth button
└── dashboard.html        # Dashboard showing workspace info

index.php                 # Main application with Slim framework
composer.json            # Dependencies and autoloader
README.md               # This documentation
```

### Key Components

#### NotionProvider (`src/NotionProvider.php`)
- Extends `League\OAuth2\Client\Provider\AbstractProvider`
- Defines Notion-specific OAuth endpoints
- Handles authorization parameters and error responses

#### NotionResourceOwner (`src/NotionResourceOwner.php`)
- Extends `League\OAuth2\Client\Provider\GenericResourceOwner`
- Provides access to workspace information from OAuth response
- Methods for getting workspace ID, name, bot ID, etc.

#### Main Application (`index.php`)
- Uses Slim Framework for clean routing
- Handles OAuth flow endpoints (`/auth`, `/callback`)
- Session management for token storage
- Integration with notion-sdk-php for API calls

## Troubleshooting

### Common Issues

1. **"Invalid redirect URI" error**:
   - Verify the redirect URI in your Notion integration matches exactly
   - Check for trailing slashes and port numbers

2. **"Invalid client ID" error**:
   - Confirm your `NOTION_CLIENT_ID` is correct
   - Make sure there are no extra spaces or quotes

3. **"Authorization failed" error**:
   - Check your `NOTION_CLIENT_SECRET` is correct
   - Ensure your integration has the required permissions

4. **Empty dashboard or no pages**:
   - Your integration might not have access to any pages
   - Share some pages with your integration in Notion
   - Check that your integration has read permissions

5. **Session issues**:
   - Make sure PHP sessions are working (`session_start()` called)
   - Check PHP session configuration
   - Clear browser cookies and try again

## Production Deployment

For production use, consider these improvements:

### Security Enhancements
- Use HTTPS for all endpoints
- Store tokens in encrypted database instead of sessions
- Implement proper session management with secure cookies
- Add rate limiting and request validation

### Code Improvements
- Add comprehensive error logging
- Implement token refresh logic if needed
- Add user management and multi-user support
- Use environment-specific configuration

### Infrastructure
- Use a proper web server (Apache/Nginx) instead of PHP dev server
- Configure proper error pages
- Set up monitoring and logging
- Use environment variables or configuration management

## API Usage Examples

Once authenticated, you can use the stored token with the notion-sdk-php library:

```php
// Create client with OAuth token
$options = (new ClientOptions())->setAuth($_SESSION['access_token']);
$notion = new Client($options);

// Search for content
$searchRequest = new SearchRequest();
$searchRequest->setQuery('my page');
$results = $notion->search()->search($searchRequest);

// Get a page
$page = $notion->pages()->retrieve('page-id');

// Query a database  
$database = $notion->databases()->query('database-id');
```
## Resources

- [Notion API Documentation](https://developers.notion.com/)
- [Notion OAuth Guide](https://developers.notion.com/docs/authorization)
- [league/oauth2-client Documentation](https://oauth2-client.thephpleague.com/)
- [Slim Framework Documentation](https://www.slimframework.com/docs/) 
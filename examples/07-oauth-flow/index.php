<?php

declare(strict_types=1);

use Dotenv\Dotenv;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use NotionOAuth\NotionProvider;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Brd6\NotionSdkPhp\Client;
use Brd6\NotionSdkPhp\ClientOptions;
use Brd6\NotionSdkPhp\Resource\Search\SearchRequest;
use Brd6\NotionSdkPhp\Resource\Page\PropertyValue\TitlePropertyValue;

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

session_start();

$app = AppFactory::create();
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

$app->get('/', function (Request $request, Response $response) {
    if (isset($_SESSION['access_token']) && !empty($_SESSION['access_token'])) {
        return showDashboard($response);
    }
    
    return showLogin($response);
});

$app->get('/auth', function (Request $request, Response $response) {
    $provider = createNotionProvider();
    
    $authUrl = $provider->getAuthorizationUrl(['owner' => 'user']);
    $_SESSION['oauth2state'] = $provider->getState();
    
    return $response
        ->withHeader('Location', $authUrl)
        ->withStatus(302);
});

$app->get('/callback', function (Request $request, Response $response) {
    $queryParams = $request->getQueryParams();
    
    if (empty($queryParams['state']) || ($queryParams['state'] !== $_SESSION['oauth2state'])) {
        unset($_SESSION['oauth2state']);
        return showError($response, 'Invalid state parameter');
    }
    
    if (empty($queryParams['code'])) {
        return showError($response, 'Authorization failed - no code received');
    }
    
    $provider = createNotionProvider();
    
    try {
        $token = $provider->getAccessToken('authorization_code', [
            'code' => $queryParams['code']
        ]);
        
        $resourceOwner = $provider->getResourceOwner($token);
        
        $_SESSION['access_token'] = $token->getToken();
        $_SESSION['workspace_name'] = $resourceOwner->getWorkspaceName();
        $_SESSION['workspace_id'] = $resourceOwner->getWorkspaceId();
        $_SESSION['workspace_icon'] = $resourceOwner->getWorkspaceIcon();
        $_SESSION['bot_id'] = $resourceOwner->getBotId();
        
        return $response
            ->withHeader('Location', '/')
            ->withStatus(302);
            
    } catch (IdentityProviderException $e) {
        return showError($response, 'OAuth error: ' . $e->getMessage());
    } catch (Exception $e) {
        return showError($response, 'Authentication failed: ' . $e->getMessage());
    }
});

$app->post('/logout', function (Request $request, Response $response) {
    session_destroy();
    return $response
        ->withHeader('Location', '/')
        ->withStatus(302);
});
function createNotionProvider(): NotionProvider
{
    return new NotionProvider([
        'clientId' => $_ENV['NOTION_CLIENT_ID'],
        'clientSecret' => $_ENV['NOTION_CLIENT_SECRET'],
        'redirectUri' => $_ENV['NOTION_REDIRECT_URI'],
    ]);
}

function showLogin(Response $response): Response
{
    $html = file_get_contents(__DIR__ . '/templates/login.html');
    $response->getBody()->write($html);
    return $response;
}

function showDashboard(Response $response): Response
{
    $html = file_get_contents(__DIR__ . '/templates/dashboard.html');
    
    $pages = [];
    try {
        $options = (new ClientOptions())->setAuth($_SESSION['access_token']);
        $notion = new Client($options);
        
        $searchRequest = new SearchRequest();
        $searchRequest->setQuery('');
        $searchRequest->setFilter([
            'value' => 'page',
            'property' => 'object'
        ]);
        
        $searchResults = $notion->search($searchRequest);
        $pages = array_slice($searchResults->getResults(), 0, 5);
    } catch (Exception $e) {
        // Continue without pages if search fails
    }
    
    $html = str_replace('{{workspace_name}}', htmlspecialchars($_SESSION['workspace_name'] ?? 'Unknown'), $html);
    $html = str_replace('{{workspace_id}}', htmlspecialchars($_SESSION['workspace_id'] ?? ''), $html);
    $html = str_replace('{{bot_id}}', htmlspecialchars($_SESSION['bot_id'] ?? ''), $html);
    
    $pagesHtml = '';
    foreach ($pages as $page) {
        $title = 'Untitled';
        $properties = $page->getProperties();
        
        foreach ($properties as $propertyName => $property) {
            if ($property instanceof TitlePropertyValue) {
                $titleRichTexts = $property->getTitle();
                if (!empty($titleRichTexts)) {
                    $title = $titleRichTexts[0]->getText()?->getContent() ?? 'Untitled';
                }
                break;
            }
        }
        
        $url = $page->getUrl();
        $pagesHtml .= sprintf(
            '<li><a href="%s" target="_blank">%s</a></li>',
            htmlspecialchars($url),
            htmlspecialchars($title)
        );
    }
    
    $html = str_replace('{{pages_list}}', $pagesHtml ?: '<li>No pages found</li>', $html);
    
    $response->getBody()->write($html);
    return $response;
}

function showError(Response $response, string $message): Response
{
    $html = sprintf(
        '<html><body><h1>Error</h1><p>%s</p><a href="/">Go back</a></body></html>',
        htmlspecialchars($message)
    );
    $response->getBody()->write($html);
    return $response->withStatus(400);
}

$app->run(); 
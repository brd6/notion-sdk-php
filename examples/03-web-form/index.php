<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/FormHandler.php';

use Brd6\NotionSdkPhp\Client;
use Brd6\NotionSdkPhp\ClientOptions;
use Brd6\NotionSdkPhp\Exception\ApiResponseException;
use Dotenv\Dotenv;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

function loadEnvironmentVariables(): void
{
    if (!file_exists(__DIR__ . '/.env')) {
        throw new RuntimeException('Environment file (.env) not found. Please copy .env.example to .env and configure your settings.');
    }
    
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}

function validateRequiredEnvironmentVariables(): void
{
    $requiredVars = ['NOTION_TOKEN', 'NOTION_DATABASE_ID'];
    
    foreach ($requiredVars as $var) {
        if (empty($_ENV[$var])) {
            throw new RuntimeException("Environment variable $var is required but not set.");
        }
    }
}

function createNotionClient(): Client
{
    $options = (new ClientOptions())->setAuth($_ENV['NOTION_TOKEN']);
    return new Client($options);
}

try {
    loadEnvironmentVariables();
    validateRequiredEnvironmentVariables();
    
    $notion = createNotionClient();
    $formHandler = new FormHandler($notion, $_ENV['NOTION_DATABASE_ID']);
    
} catch (Exception $e) {
    die("Configuration Error: " . $e->getMessage() . "\nPlease check your .env file and ensure all required variables are set.");
}

$app = AppFactory::create();

$twig = Twig::create(__DIR__ . '/templates', ['cache' => false]);
$app->add(TwigMiddleware::create($app, $twig));

$app->get('/', function (Request $request, Response $response) {
    $view = Twig::fromRequest($request);
    return $view->render($response, 'form.html');
});

$app->post('/submit', function (Request $request, Response $response) use ($formHandler) {
    $view = Twig::fromRequest($request);
    $data = $request->getParsedBody();
    
    try {
        $validatedData = $formHandler->validateFormData($data);
        $pageUrl = $formHandler->createNotionPage($validatedData);
        
        return $response
            ->withHeader('Location', '/success?url=' . urlencode($pageUrl))
            ->withStatus(302);
            
    } catch (InvalidArgumentException $e) {
        return $view->render($response, 'form.html', [
            'error' => $e->getMessage(),
            'name' => $data['name'] ?? '',
            'email' => $data['email'] ?? '',
            'message' => $data['message'] ?? ''
        ])->withStatus(400);
        
    } catch (ApiResponseException $e) {
        return $view->render($response, 'form.html', [
            'error' => 'Failed to create page in Notion: ' . $e->getMessage(),
            'name' => $data['name'] ?? '',
            'email' => $data['email'] ?? '',
            'message' => $data['message'] ?? ''
        ])->withStatus(500);
        
    } catch (Exception $e) {
        return $view->render($response, 'form.html', [
            'error' => 'An unexpected error occurred. Please try again.',
            'name' => $data['name'] ?? '',
            'email' => $data['email'] ?? '',
            'message' => $data['message'] ?? ''
        ])->withStatus(500);
    }
});

$app->get('/success', function (Request $request, Response $response) {
    $view = Twig::fromRequest($request);
    $pageUrl = $request->getQueryParams()['url'] ?? null;
    
    return $view->render($response, 'success.html', [
        'pageUrl' => $pageUrl
    ]);
});

$app->addErrorMiddleware(true, true, true);

$app->run(); 
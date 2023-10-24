<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\HttpClient;

use Brd6\NotionSdkPhp\ClientOptions;
use Http\Adapter\Guzzle7\Client as GuzzleHttpClient;
use Http\Client\Common\HttpMethodsClient;
use Http\Client\Common\HttpMethodsClientInterface;
use Http\Client\Common\Plugin\AuthenticationPlugin;
use Http\Client\Common\Plugin\BaseUriPlugin;
use Http\Client\Common\Plugin\HeaderSetPlugin;
use Http\Client\Common\PluginClientFactory;
use Http\Client\Curl\Client as CurlHttpClient;
use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use Http\Message\Authentication\Bearer as AuthenticationBearer;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use Symfony\Component\HttpClient\HttpClient as SymfonyHttpClient;
use Symfony\Component\HttpClient\HttplugClient as SymfonyHttplugClient;

use function class_exists;

use const CURLOPT_TIMEOUT;

class HttpClientFactory implements HttpClientFactoryInterface
{
    private RequestFactoryInterface $requestFactory;
    private UriFactoryInterface $uriFactory;
    private ?StreamFactoryInterface $streamFactory;

    public function __construct(
        ?RequestFactoryInterface $requestFactory = null,
        ?UriFactoryInterface $uriFactory = null,
        ?StreamFactoryInterface $streamFactory = null,
    ) {
        $this->requestFactory = $requestFactory ?: Psr17FactoryDiscovery::findRequestFactory();
        $this->uriFactory = $uriFactory ?: Psr17FactoryDiscovery::findUriFactory();
        $this->streamFactory = $streamFactory ?: Psr17FactoryDiscovery::findStreamFactory();
    }

    public function create(ClientOptions $options): HttpMethodsClientInterface
    {
        $httpClient = $options->getHttpClient() ?? $this->resolveHttpClient($options);

        $headers = [
            'Notion-Version' => $options->getNotionVersion(),
            'Content-Type' => 'application/json',
            'User-Agent' => 'brd6/notion-sdk-php (https://github.com/brd6/notion-sdk-php)',
        ];

        $plugins = [
            new BaseUriPlugin($this->uriFactory->createUri($options->getBaseUrl())),
            new HeaderSetPlugin($headers),
        ];

        if ($options->hasAuth()) {
            $plugins[] = new AuthenticationPlugin(new AuthenticationBearer($options->getAuth()));
        }

        return new HttpMethodsClient(
            (new PluginClientFactory())->createClient($httpClient, $plugins),
            $this->requestFactory,
            $this->streamFactory,
        );
    }

    private function resolveHttpClient(ClientOptions $options): ClientInterface
    {
        if (class_exists(SymfonyHttplugClient::class)) {
            $httpClient = $this->createHttpClientForSymfony($options);
        } elseif (class_exists(GuzzleHttpClient::class)) {
            $httpClient = $this->createHttpClientForGuzzle($options);
        } elseif (class_exists(CurlHttpClient::class)) {
            $httpClient = $this->createHttpClientForCurl($options);
        } else {
            $httpClient = Psr18ClientDiscovery::find();
        }

        return $httpClient;
    }

    private function createHttpClientForSymfony(ClientOptions $options): ClientInterface
    {
        $httpOptions = [
            'timeout' => $options->getTimeout(),
        ];

        return new SymfonyHttplugClient(SymfonyHttpClient::create($httpOptions));
    }

    /**
     * @psalm-suppress MixedInferredReturnType
     * @psalm-suppress MixedReturnStatement
     * @psalm-suppress UnusedVariable
     * @psalm-suppress UndefinedClass
     * @psalm-suppress InvalidReturnType
     * @psalm-suppress InvalidReturnStatement
     */
    private function createHttpClientForGuzzle(ClientOptions $options): ClientInterface
    {
        $httpOptions = [
            'timeout' => $options->getTimeout(),
        ];

        // @phpstan-ignore-next-line
        return GuzzleHttpClient::createWithConfig($httpOptions);
    }

    /**
     * @psalm-suppress MixedInferredReturnType
     * @psalm-suppress MixedReturnStatement
     * @psalm-suppress UnusedVariable
     * @psalm-suppress UndefinedClass
     * @psalm-suppress InvalidReturnType
     * @psalm-suppress InvalidReturnStatement
     */
    private function createHttpClientForCurl(ClientOptions $options): ClientInterface
    {
        $httpOptions = [
            CURLOPT_TIMEOUT => $options->getTimeout(),
        ];

        // @phpstan-ignore-next-line
        return new CurlHttpClient(null, null, $httpOptions);
    }
}

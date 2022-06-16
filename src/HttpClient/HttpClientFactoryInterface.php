<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\HttpClient;

use Brd6\NotionSdkPhp\ClientOptions;
use Http\Client\Common\HttpMethodsClientInterface;

interface HttpClientFactoryInterface
{
    public function create(ClientOptions $options): HttpMethodsClientInterface;
}

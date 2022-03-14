<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Endpoint;

use Brd6\NotionSdkPhp\Client;

abstract class AbstractEndpoint
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    protected function getClient(): Client
    {
        return $this->client;
    }
}

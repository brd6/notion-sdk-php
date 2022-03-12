<?php

declare(strict_types=1);

namespace Brd6\Test\NotionSdkPhp;

use Brd6\NotionSdkPhp\Client;

class ClientTest extends TestCase
{
    public function testInstance(): void
    {
        $this->assertInstanceOf(Client::class, new Client());
    }
}

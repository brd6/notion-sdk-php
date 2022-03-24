<?php

declare(strict_types=1);

namespace Brd6\Test\NotionSdkPhp\Endpoint;

use Brd6\NotionSdkPhp\Client;
use Brd6\NotionSdkPhp\ClientOptions;
use Brd6\NotionSdkPhp\Endpoint\UsersEndpoint;
use Brd6\NotionSdkPhp\Resource\User\PersonUser;
use Brd6\Test\NotionSdkPhp\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

use function file_get_contents;

class UsersEndpointTest extends TestCase
{
    public function testInstance(): void
    {
        $client = new Client();
        $users = new UsersEndpoint($client);

        $this->assertInstanceOf(UsersEndpoint::class, $client->users());
        $this->assertInstanceOf(UsersEndpoint::class, $users);
    }

    public function testRetrieve(): void
    {
        $httpClient = new MockHttpClient();
        $httpClient->setResponseFactory([
            new MockResponse(
                (string) file_get_contents('tests/fixtures/client_users_retrieve_user_200.json'),
                [
                    'http_code' => 200,
                ],
            ),
        ]);

        $options = (new ClientOptions())
            ->setAuth('secret_valid-auth')
            ->setHttpClient($httpClient);

        $client = new Client($options);

        /** @var PersonUser $user */
        $user = $client->users()->retrieve('7f03dda0-a132-49d7-b8b2-29c9ed1b1f0e');

        $this->assertEquals('person', $user::getResourceType());
        $this->assertNotEmpty($user->getType());
        $this->assertNotEmpty($user->getName());
    }
}

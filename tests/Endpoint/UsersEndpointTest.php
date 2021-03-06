<?php

declare(strict_types=1);

namespace Brd6\Test\NotionSdkPhp\Endpoint;

use Brd6\NotionSdkPhp\Client;
use Brd6\NotionSdkPhp\ClientOptions;
use Brd6\NotionSdkPhp\Endpoint\UsersEndpoint;
use Brd6\NotionSdkPhp\Resource\Pagination\UserResults;
use Brd6\NotionSdkPhp\Resource\User\BotUser;
use Brd6\NotionSdkPhp\Resource\User\PersonUser;
use Brd6\Test\NotionSdkPhp\Mock\HttpClient\MockHttpClient;
use Brd6\Test\NotionSdkPhp\Mock\HttpClient\MockResponseFactory;
use Brd6\Test\NotionSdkPhp\TestCase;

use function count;
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
        $httpClient = new MockHttpClient(
            new MockResponseFactory(
                (string) file_get_contents('tests/Fixtures/client_users_retrieve_user_200.json'),
                [
                    'http_code' => 200,
                ],
            ),
        );

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

    public function testRetrieveUsersList(): void
    {
        $httpClient = new MockHttpClient(function ($method, $url, $options) {
            if ($method === 'GET') {
                $this->assertStringContainsString('users', $url);
                $this->assertStringContainsString('page_size', $url);
                $this->assertArrayHasKey('page_size', $options['query']);
                $this->assertNotEmpty($options['query']['page_size']);
            }

            return new MockResponseFactory(
                (string) file_get_contents('tests/Fixtures/client_users_retrieve_list_default_200.json'),
                [
                    'http_code' => 200,
                ],
            );
        });

        $options = (new ClientOptions())
            ->setAuth('secret_valid-auth')
            ->setHttpClient($httpClient);

        $client = new Client($options);

        /** @var UserResults $paginationResponse */
        $paginationResponse = $client->users()->list();

        $this->assertNotNull($paginationResponse);
        $this->assertInstanceOf(UserResults::class, $paginationResponse);

        $this->assertEquals('user', $paginationResponse->getType());
        $this->assertEquals('list', $paginationResponse->getObject());
        $this->assertGreaterThan(0, count($paginationResponse->getResults()));

        $resultUser = $paginationResponse->getResults()[0];

        $this->assertEquals('user', $resultUser->getObject());
        $this->assertNotEmpty($resultUser->getId());
    }

    public function testRetrieveBotUser(): void
    {
        $httpClient = new MockHttpClient(
            new MockResponseFactory(
                (string) file_get_contents('tests/Fixtures/client_users_retrieve_bot_200.json'),
                [
                    'http_code' => 200,
                ],
            ),
        );

        $options = (new ClientOptions())
            ->setAuth('secret_valid-auth')
            ->setHttpClient($httpClient);

        $client = new Client($options);

        /** @var BotUser $user */
        $user = $client->users()->me();

        $this->assertEquals('bot', $user::getResourceType());
        $this->assertNotEmpty($user->getType());
        $this->assertNotEmpty($user->getName());
    }
}

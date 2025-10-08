<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Tests\Resource\User;

use Brd6\NotionSdkPhp\Resource\User\BotUser;
use PHPUnit\Framework\TestCase;

class BotUserTest extends TestCase
{
    public function testFromRawDataWithEmptyBotObject(): void
    {
        $rawData = [
            'object' => 'user',
            'id' => 'd0063369-46a4-4756-8798-0d36d53c9b20',
            'name' => 'sdk-php-test',
            'avatar_url' => null,
            'type' => 'bot',
            'bot' => [],
        ];

        $botUser = BotUser::fromRawData($rawData);

        $this->assertInstanceOf(BotUser::class, $botUser);
        $this->assertSame('d0063369-46a4-4756-8798-0d36d53c9b20', $botUser->getId());
        $this->assertSame('sdk-php-test', $botUser->getName());
        $this->assertSame('bot', $botUser->getType());
        $this->assertNull($botUser->getAvatarUrl());
        $this->assertNotNull($botUser->getBot());
        $this->assertNull($botUser->getBot()->getOwner());
    }

    public function testFromRawDataWithFullBotObject(): void
    {
        $rawData = [
            'object' => 'user',
            'id' => '16d84278-ab0e-484c-9bdd-b35da3bd8905',
            'name' => 'pied piper',
            'avatar_url' => null,
            'type' => 'bot',
            'bot' => [
                'owner' => [
                    'type' => 'user',
                    'user' => [
                        'object' => 'user',
                        'id' => '5389a034-eb5c-47b5-8a9e-f79c99ef166c',
                        'name' => 'christine makenotion',
                        'avatar_url' => null,
                        'type' => 'person',
                        'person' => [
                            'email' => 'christine@makenotion.com',
                        ],
                    ],
                ],
            ],
        ];

        $botUser = BotUser::fromRawData($rawData);

        $this->assertInstanceOf(BotUser::class, $botUser);
        $this->assertSame('16d84278-ab0e-484c-9bdd-b35da3bd8905', $botUser->getId());
        $this->assertSame('pied piper', $botUser->getName());
        $this->assertSame('bot', $botUser->getType());
        $this->assertNotNull($botUser->getBot());
        $this->assertNotNull($botUser->getBot()->getOwner());
        $this->assertSame('user', $botUser->getBot()->getOwner()->getType());
        $this->assertNotNull($botUser->getBot()->getOwner()->getUser());
        $this->assertSame('5389a034-eb5c-47b5-8a9e-f79c99ef166c', $botUser->getBot()->getOwner()->getUser()->getId());
    }
}

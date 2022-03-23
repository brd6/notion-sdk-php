<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\User;

use Brd6\NotionSdkPhp\Resource\Property\BotProperty;

class BotUser extends AbstractUser
{
    public const RESOURCE_TYPE = 'bot';

    protected ?BotProperty $bot = null;

    protected function initialize(): void
    {
        $this->bot = BotProperty::fromRawData((array) $this->getRawData()[(string) $this->getType()]);
    }

    public static function getResourceType(): string
    {
        return self::RESOURCE_TYPE;
    }

    public function getBot(): ?BotProperty
    {
        return $this->bot;
    }

    public function setBot(?BotProperty $bot): self
    {
        $this->bot = $bot;

        return $this;
    }
}

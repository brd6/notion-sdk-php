<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\User;

class PartialUser extends AbstractUser
{
    public const RESOURCE_TYPE = 'partial_user';

    protected function initialize(): void
    {
    }

    public static function getResourceType(): string
    {
        return self::RESOURCE_TYPE;
    }
}

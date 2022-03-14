<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource;

class PartialUser extends AbstractResource
{
    public const RESOURCE_TYPE = 'user';

    public static function fromRawData(array $rawData): self
    {
        /** @var self $resource */
        $resource = parent::fromRawData($rawData);

        return $resource;
    }

    protected function initialize(): void
    {
    }

    public static function getResourceType(): string
    {
        return self::RESOURCE_TYPE;
    }
}

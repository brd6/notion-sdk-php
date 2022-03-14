<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource;

class PartialUser extends AbstractResource
{
    public const RESOURCE_TYPE = 'user';

    public static function fromResponseData(array $responseData): self
    {
        /** @var self $resource */
        $resource = parent::fromResponseData($responseData);

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

<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource;

interface ResourceInterface
{
    public static function fromRawData(array $rawData): self;

    public static function getResourceType(): string;

    public function getRawData(): array;

    public function getObject(): string;

    public function getId(): string;
}

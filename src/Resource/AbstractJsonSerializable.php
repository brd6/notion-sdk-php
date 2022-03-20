<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource;

use Brd6\NotionSdkPhp\Util\ArrayHelper;
use JsonSerializable;

use function array_filter;
use function get_object_vars;
use function json_decode;
use function json_encode;

use const ARRAY_FILTER_USE_KEY;

abstract class AbstractJsonSerializable implements JsonSerializable
{
    public function jsonSerialize(): array
    {
        return array_filter(get_object_vars($this), fn (string $key) => $key !== 'rawData', ARRAY_FILTER_USE_KEY);
    }

    public function toJson(): array
    {
        /** @var array $data */
        $data = json_decode((string) json_encode($this), true);
        ArrayHelper::transformKeysToSnakeCase($data);

        return $data;
    }
}

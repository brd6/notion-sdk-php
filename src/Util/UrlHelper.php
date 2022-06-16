<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Util;

use function http_build_query;
use function parse_str;

use const PHP_QUERY_RFC3986;

abstract class UrlHelper
{
    public static function buildQuery(array $params): string
    {
        return http_build_query(
            $params,
            '',
            '&',
            PHP_QUERY_RFC3986,
        );
    }

    public static function parseQuery(string $query): array
    {
        $params = [];
        parse_str($query, $params);

        return $params;
    }
}

<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Util;

use function ltrim;
use function preg_replace;
use function str_replace;
use function strtolower;
use function ucwords;

class StringHelper
{
    public static function snakeCaseToCamelCase(string $text, string $separator = '_'): string
    {
        return str_replace($separator, '', ucwords($text, $separator));
    }

    public static function camelCaseToSnakeCase(string $text): string
    {
        return strtolower(
            (string) preg_replace(
                '/([a-z0-9])([A-Z0-9])/',
                '$1_$2',
                ltrim($text, '!'),
            ),
        );
    }
}

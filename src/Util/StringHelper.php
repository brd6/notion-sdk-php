<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Util;

use function str_replace;
use function ucwords;

class StringHelper
{
    public static function snakeCaseToCamelCase(string $text, string $separator = '_'): string
    {
        return str_replace($separator, '', ucwords($text, $separator));
    }
}

<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Util;

use function array_keys;
use function is_array;
use function ltrim;
use function preg_replace;
use function strtolower;

class ArrayHelper
{
    /**
     * @psalm-suppress UnnecessaryVarAnnotation, MixedAssignment
     */
    public static function transformKeysToSnakeCase(array &$data): void
    {
        foreach (array_keys($data) as $key) {
            $value = &$data[$key];
            unset($data[$key]);

            $transformedKey = strtolower(
                (string) preg_replace(
                    '/([a-z0-9])([A-Z0-9])/',
                    '$1_$2',
                    ltrim((string) $key, '!'),
                ),
            );

            if (is_array($value)) {
                self::transformKeysToSnakeCase($value);
            }

            $data[$transformedKey] = $value;
            unset($value);
        }
    }
}

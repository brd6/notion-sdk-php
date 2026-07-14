<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Util;

use function array_keys;
use function is_array;

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

            $transformedKey = StringHelper::camelCaseToSnakeCase((string) $key);

            if (is_array($value)) {
                if ($transformedKey === 'properties') {
                    self::transformChildValueKeysToSnakeCase($value);
                } else {
                    self::transformKeysToSnakeCase($value);
                }
            }

            $data[$transformedKey] = $value;
            unset($value);
        }
    }

    /**
     * Keys of a `properties` map are user-defined property names, not fields to transform.
     *
     * @psalm-suppress MixedAssignment
     */
    private static function transformChildValueKeysToSnakeCase(array &$data): void
    {
        foreach (array_keys($data) as $key) {
            $value = &$data[$key];

            if (is_array($value)) {
                self::transformKeysToSnakeCase($value);
            }

            unset($value);
        }
    }
}

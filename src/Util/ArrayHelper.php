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
                self::transformKeysToSnakeCase($value);
            }

            $data[$transformedKey] = $value;
            unset($value);
        }
    }
}

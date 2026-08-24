<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Exception;

use function sprintf;
use function strlen;

class UnsupportedPropertyTypeException extends AbstractNotionException
{
    public const MESSAGE = 'The given property type "%s" is unsupported for "%s".';

    public function __construct(string $type, string $baseType, string $message = '')
    {
        $message = strlen($message) > 0 ? $message : sprintf(self::MESSAGE, $type, $baseType);

        parent::__construct($message);
    }

    public function getMessageCode(): string
    {
        return '';
    }
}

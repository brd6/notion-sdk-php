<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Exception;

use function sprintf;
use function strlen;

class InvalidPropertyException extends AbstractNotionException
{
    public const MESSAGE = 'The given property is invalid for %s.';

    public function __construct(string $baseType, string $message = self::MESSAGE)
    {
        $message = strlen($message) > 0 ? $message : sprintf(self::MESSAGE, $baseType);

        parent::__construct($message);
    }

    public function getMessageCode(): string
    {
        return '';
    }
}

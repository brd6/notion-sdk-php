<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Exception;

class InvalidPropertyItemException extends AbstractNotionException
{
    public const MESSAGE = 'The given property item is invalid.';

    public function __construct(string $message = self::MESSAGE)
    {
        parent::__construct($message);
    }

    public function getMessageCode(): string
    {
        return '';
    }
}

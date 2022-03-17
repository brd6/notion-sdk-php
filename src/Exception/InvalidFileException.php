<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Exception;

class InvalidFileException extends AbstractNotionException
{
    public const MESSAGE = 'The given file is invalid.';

    public function __construct(string $message = self::MESSAGE)
    {
        parent::__construct($message);
    }

    public function getMessageCode(): string
    {
        return '';
    }
}

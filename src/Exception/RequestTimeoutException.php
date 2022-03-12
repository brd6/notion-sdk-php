<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Exception;

use Brd6\NotionSdkPhp\Constant\NotionErrorCodeConstant;

class RequestTimeoutException extends AbstractNotionException
{
    public const MESSAGE = 'Request to Notion API has timed out';

    public function __construct(string $message = self::MESSAGE)
    {
        parent::__construct($message);
    }

    public function getMessageCode(): string
    {
        return NotionErrorCodeConstant::REQUEST_TIMEOUT;
    }
}

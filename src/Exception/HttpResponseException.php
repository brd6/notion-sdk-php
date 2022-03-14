<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Exception;

use Brd6\NotionSdkPhp\Constant\NotionErrorCodeConstant;

use function sprintf;
use function strlen;

class HttpResponseException extends AbstractNotionException
{
    public const MESSAGE = 'Request to Notion API failed with status: %s';
    private array $rawData;
    private array $headers;

    public function __construct(int $statusCode, array $headers = [], array $rawData = [], string $message = '')
    {
        $message = strlen($message) > 0 ? $message : sprintf(self::MESSAGE, $statusCode);

        parent::__construct($message);

        $this->rawData = $rawData;
        $this->headers = $headers;
    }

    public function getMessageCode(): string
    {
        return NotionErrorCodeConstant::RESPONSE_ERROR;
    }

    public function getRawData(): array
    {
        return $this->rawData;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }
}

<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Exception;

use function strlen;

class ApiResponseException extends HttpResponseException
{
    private string $messageCode;
    private int $status;

    public function __construct(int $statusCode, array $headers = [], array $responseData = [], string $message = '')
    {
        $this->messageCode = (string) $responseData['code'];
        $this->status = (int) $responseData['status'];

        $message = strlen($message) > 0 ? $message : (string) $responseData['message'];

        parent::__construct($statusCode, $headers, $responseData, $message);
    }

    public function getMessageCode(): string
    {
        return $this->messageCode;
    }

    public function getStatus(): int
    {
        return $this->status;
    }
}

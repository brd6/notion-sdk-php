<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Endpoint;

use Brd6\NotionSdkPhp\Exception\ApiResponseException;
use Brd6\NotionSdkPhp\Exception\HttpResponseException;
use Brd6\NotionSdkPhp\Exception\InvalidResourceException;
use Brd6\NotionSdkPhp\Exception\InvalidResourceTypeException;
use Brd6\NotionSdkPhp\Exception\RequestTimeoutException;
use Brd6\NotionSdkPhp\RequestParameters;
use Brd6\NotionSdkPhp\Resource\AsyncTask;
use Http\Client\Exception;

class AsyncTasksEndpoint extends AbstractEndpoint
{
    /**
     * Requires Notion-Version 2026-03-11. Polling cadence is the caller's responsibility;
     * the task's poll_after_seconds carries the server's guidance.
     *
     * @throws ApiResponseException
     * @throws Exception
     * @throws HttpResponseException
     * @throws InvalidResourceException
     * @throws InvalidResourceTypeException
     * @throws RequestTimeoutException
     */
    public function retrieve(string $taskId): AsyncTask
    {
        $requestParameters = (new RequestParameters())
            ->setPath("async_tasks/$taskId")
            ->setMethod('GET');

        $rawData = $this->getClient()->request($requestParameters);

        /** @var AsyncTask $asyncTask */
        $asyncTask = AsyncTask::fromRawData($rawData);

        return $asyncTask;
    }
}

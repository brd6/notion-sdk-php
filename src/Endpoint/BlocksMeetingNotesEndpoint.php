<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Endpoint;

use Brd6\NotionSdkPhp\Exception\ApiResponseException;
use Brd6\NotionSdkPhp\Exception\HttpResponseException;
use Brd6\NotionSdkPhp\Exception\InvalidResourceException;
use Brd6\NotionSdkPhp\Exception\InvalidResourceTypeException;
use Brd6\NotionSdkPhp\Exception\RequestTimeoutException;
use Brd6\NotionSdkPhp\Exception\UnsupportedUserTypeException;
use Brd6\NotionSdkPhp\RequestParameters;
use Brd6\NotionSdkPhp\Resource\Block\MeetingNotesQueryRequest;
use Brd6\NotionSdkPhp\Resource\Block\MeetingNotesQueryResults;
use Http\Client\Exception;

class BlocksMeetingNotesEndpoint extends AbstractEndpoint
{
    /**
     * Queries AI meeting notes across the workspace. Requires Notion-Version 2026-03-11.
     *
     * @throws ApiResponseException
     * @throws Exception
     * @throws HttpResponseException
     * @throws InvalidResourceException
     * @throws InvalidResourceTypeException
     * @throws RequestTimeoutException
     * @throws UnsupportedUserTypeException
     */
    public function query(?MeetingNotesQueryRequest $queryRequest = null): MeetingNotesQueryResults
    {
        $requestParameters = (new RequestParameters())
            ->setPath('blocks/meeting_notes/query')
            ->setMethod('POST')
            ->setBody($queryRequest ? $queryRequest->toArray() : []);

        $rawData = $this->getClient()->request($requestParameters);

        return MeetingNotesQueryResults::fromRawData($rawData);
    }
}

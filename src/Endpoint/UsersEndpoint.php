<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Endpoint;

use Brd6\NotionSdkPhp\Exception\ApiResponseException;
use Brd6\NotionSdkPhp\Exception\HttpResponseException;
use Brd6\NotionSdkPhp\Exception\InvalidPaginationResponseException;
use Brd6\NotionSdkPhp\Exception\RequestTimeoutException;
use Brd6\NotionSdkPhp\Exception\UnsupportedPaginationResponseTypeException;
use Brd6\NotionSdkPhp\Exception\UnsupportedUserTypeException;
use Brd6\NotionSdkPhp\RequestParameters;
use Brd6\NotionSdkPhp\Resource\Pagination\AbstractPaginationResults;
use Brd6\NotionSdkPhp\Resource\Pagination\PaginationRequest;
use Brd6\NotionSdkPhp\Resource\User\AbstractUser;

class UsersEndpoint extends AbstractEndpoint
{
    /**
     * @throws ApiResponseException
     * @throws HttpResponseException
     * @throws RequestTimeoutException
     * @throws UnsupportedUserTypeException
     */
    public function retrieve(string $userId): AbstractUser
    {
        $requestParameters = (new RequestParameters())
            ->setPath("users/$userId")
            ->setMethod('GET');

        $rawData = $this->getClient()->request($requestParameters);

        return AbstractUser::fromRawData($rawData);
    }

    /**
     * @throws ApiResponseException
     * @throws HttpResponseException
     * @throws InvalidPaginationResponseException
     * @throws RequestTimeoutException
     * @throws UnsupportedPaginationResponseTypeException
     */
    public function list(?PaginationRequest $paginationRequest = null): AbstractPaginationResults
    {
        $paginationRequest = $paginationRequest ?? new PaginationRequest();

        $requestParameters = (new RequestParameters())
            ->setPath('users')
            ->setQuery($paginationRequest->toJson())
            ->setMethod('GET');

        $rawData = $this->getClient()->request($requestParameters);

        return AbstractPaginationResults::fromRawData($rawData);
    }

    /**
     * @throws ApiResponseException
     * @throws HttpResponseException
     * @throws RequestTimeoutException
     * @throws UnsupportedUserTypeException
     */
    public function me(): AbstractUser
    {
        $requestParameters = (new RequestParameters())
            ->setPath('users/me')
            ->setMethod('GET');

        $rawData = $this->getClient()->request($requestParameters);

        return AbstractUser::fromRawData($rawData);
    }
}

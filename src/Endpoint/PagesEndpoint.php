<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Endpoint;

use Brd6\NotionSdkPhp\Exception\ApiResponseException;
use Brd6\NotionSdkPhp\Exception\HttpResponseException;
use Brd6\NotionSdkPhp\Exception\InvalidResourceException;
use Brd6\NotionSdkPhp\Exception\InvalidResourceTypeException;
use Brd6\NotionSdkPhp\Exception\RequestTimeoutException;
use Brd6\NotionSdkPhp\RequestParameters;
use Brd6\NotionSdkPhp\Resource\Page;

class PagesEndpoint extends AbstractEndpoint
{
    /**
     * @throws ApiResponseException
     * @throws HttpResponseException
     * @throws RequestTimeoutException
     * @throws InvalidResourceException
     * @throws InvalidResourceTypeException
     */
    public function retrieve(string $pageId): Page
    {
        $requestParameters = (new RequestParameters())
            ->setPath("pages/$pageId")
            ->setMethod('GET');

        $rawData = $this->getClient()->request($requestParameters);

        /** @var Page $page */
        $page = Page::fromRawData($rawData);

        return $page;
    }
}

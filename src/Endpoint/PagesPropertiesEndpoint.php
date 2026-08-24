<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Endpoint;

use Brd6\NotionSdkPhp\Exception\ApiResponseException;
use Brd6\NotionSdkPhp\Exception\HttpResponseException;
use Brd6\NotionSdkPhp\Exception\InvalidPaginationResponseException;
use Brd6\NotionSdkPhp\Exception\InvalidPropertyValueException;
use Brd6\NotionSdkPhp\Exception\InvalidResourceException;
use Brd6\NotionSdkPhp\Exception\RequestTimeoutException;
use Brd6\NotionSdkPhp\Exception\UnsupportedPaginationResponseTypeException;
use Brd6\NotionSdkPhp\Exception\UnsupportedPropertyValueException;
use Brd6\NotionSdkPhp\RequestParameters;
use Brd6\NotionSdkPhp\Resource\Page\PropertyValue\AbstractPropertyValue;
use Brd6\NotionSdkPhp\Resource\Pagination\AbstractPaginationResults;
use Brd6\NotionSdkPhp\Resource\Pagination\PaginationRequest;
use Http\Client\Exception;

class PagesPropertiesEndpoint extends AbstractEndpoint
{
    public const PROPERTY_ITEM_TYPE = 'property_item';

    /**
     * @param string $pageId
     * @param string $propertyId
     * @param PaginationRequest|null $paginationRequest
     *
     * @return AbstractPropertyValue|AbstractPaginationResults
     *
     * @throws ApiResponseException
     * @throws HttpResponseException
     * @throws InvalidPaginationResponseException
     * @throws InvalidPropertyValueException
     * @throws InvalidResourceException
     * @throws RequestTimeoutException
     * @throws UnsupportedPaginationResponseTypeException
     * @throws UnsupportedPropertyValueException
     * @throws Exception
     */
    public function retrieve(
        string $pageId,
        string $propertyId,
        ?PaginationRequest $paginationRequest = null
    ) {
        return $this->retrieveProperty($pageId, $propertyId, $paginationRequest, false);
    }

    /**
     * @return AbstractPropertyValue|AbstractPaginationResults
     */
    public function retrieveWithUnsupportedContentFallback(
        string $pageId,
        string $propertyId,
        ?PaginationRequest $paginationRequest = null
    ) {
        return $this->retrieveProperty($pageId, $propertyId, $paginationRequest, true);
    }

    /**
     * @return AbstractPropertyValue|AbstractPaginationResults
     */
    private function retrieveProperty(
        string $pageId,
        string $propertyId,
        ?PaginationRequest $paginationRequest,
        bool $fallbackOnUnsupportedContent
    ) {
        $paginationRequest = $paginationRequest ?? new PaginationRequest();

        $requestParameters = (new RequestParameters())
            ->setPath("pages/$pageId/properties/$propertyId")
            ->setQuery($paginationRequest->toArray())
            ->setMethod('GET');

        $rawData = $this->getClient()->request($requestParameters);

        return $this->transformRawData($rawData, $fallbackOnUnsupportedContent);
    }

    /**
     * @return AbstractPaginationResults|AbstractPropertyValue
     *
     * @throws UnsupportedPropertyValueException
     * @throws UnsupportedPaginationResponseTypeException
     * @throws InvalidPaginationResponseException
     * @throws InvalidPropertyValueException
     * @throws InvalidResourceException
     */
    private function transformRawData(array $rawData, bool $fallbackOnUnsupportedContent)
    {
        if (!isset($rawData['type'])) {
            throw new InvalidResourceException();
        }

        return $rawData['type'] === self::PROPERTY_ITEM_TYPE ?
            AbstractPaginationResults::fromRawData($rawData) :
            ($fallbackOnUnsupportedContent ?
                AbstractPropertyValue::fromRawDataWithUnsupportedContentFallback($rawData) :
                AbstractPropertyValue::fromRawData($rawData));
    }
}

<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Endpoint;

use Brd6\NotionSdkPhp\Exception\ApiResponseException;
use Brd6\NotionSdkPhp\Exception\HttpResponseException;
use Brd6\NotionSdkPhp\Exception\InvalidPaginationResponseException;
use Brd6\NotionSdkPhp\Exception\InvalidResourceException;
use Brd6\NotionSdkPhp\Exception\InvalidResourceTypeException;
use Brd6\NotionSdkPhp\Exception\RequestTimeoutException;
use Brd6\NotionSdkPhp\Exception\UnsupportedPaginationResponseTypeException;
use Brd6\NotionSdkPhp\RequestParameters;
use Brd6\NotionSdkPhp\Resource\FileUpload;
use Brd6\NotionSdkPhp\Resource\FileUpload\FileUploadListRequest;
use Brd6\NotionSdkPhp\Resource\FileUpload\FileUploadRequest;
use Brd6\NotionSdkPhp\Resource\Pagination\AbstractPaginationResults;
use Brd6\NotionSdkPhp\Resource\Pagination\PaginationRequest;
use Http\Client\Exception;
use Http\Message\MultipartStream\MultipartStreamBuilder;

use function array_filter;
use function array_merge;

class FileUploadsEndpoint extends AbstractEndpoint
{
    /**
     * @throws ApiResponseException
     * @throws Exception
     * @throws HttpResponseException
     * @throws InvalidResourceException
     * @throws InvalidResourceTypeException
     * @throws RequestTimeoutException
     */
    public function create(FileUploadRequest $fileUploadRequest): FileUpload
    {
        $requestParameters = (new RequestParameters())
            ->setPath('file_uploads')
            ->setMethod('POST')
            ->setBody($fileUploadRequest->toArray());

        $rawData = $this->getClient()->request($requestParameters);

        /** @var FileUpload $fileUpload */
        $fileUpload = FileUpload::fromRawData($rawData);

        return $fileUpload;
    }

    /**
     * @throws ApiResponseException
     * @throws Exception
     * @throws HttpResponseException
     * @throws InvalidResourceException
     * @throws InvalidResourceTypeException
     * @throws RequestTimeoutException
     */
    public function send(
        string $fileUploadId,
        string $contents,
        string $filename,
        ?string $contentType = null,
        ?int $partNumber = null
    ): FileUpload {
        $builder = new MultipartStreamBuilder();
        $builder->addResource('file', $contents, array_filter([
            'filename' => $filename,
            'headers' => $contentType !== null ? ['Content-Type' => $contentType] : null,
        ]));

        if ($partNumber !== null) {
            $builder->addResource('part_number', (string) $partNumber);
        }

        $requestParameters = (new RequestParameters())
            ->setPath("file_uploads/$fileUploadId/send")
            ->setMethod('POST')
            ->setHeaders([
                'Content-Type' => 'multipart/form-data; boundary="' . $builder->getBoundary() . '"',
            ])
            ->setRawBody((string) $builder->build());

        $rawData = $this->getClient()->request($requestParameters);

        /** @var FileUpload $fileUpload */
        $fileUpload = FileUpload::fromRawData($rawData);

        return $fileUpload;
    }

    /**
     * @throws ApiResponseException
     * @throws Exception
     * @throws HttpResponseException
     * @throws InvalidResourceException
     * @throws InvalidResourceTypeException
     * @throws RequestTimeoutException
     */
    public function complete(string $fileUploadId): FileUpload
    {
        $requestParameters = (new RequestParameters())
            ->setPath("file_uploads/$fileUploadId/complete")
            ->setMethod('POST');

        $rawData = $this->getClient()->request($requestParameters);

        /** @var FileUpload $fileUpload */
        $fileUpload = FileUpload::fromRawData($rawData);

        return $fileUpload;
    }

    /**
     * @throws ApiResponseException
     * @throws Exception
     * @throws HttpResponseException
     * @throws InvalidResourceException
     * @throws InvalidResourceTypeException
     * @throws RequestTimeoutException
     */
    public function retrieve(string $fileUploadId): FileUpload
    {
        $requestParameters = (new RequestParameters())
            ->setPath("file_uploads/$fileUploadId")
            ->setMethod('GET');

        $rawData = $this->getClient()->request($requestParameters);

        /** @var FileUpload $fileUpload */
        $fileUpload = FileUpload::fromRawData($rawData);

        return $fileUpload;
    }

    /**
     * @throws ApiResponseException
     * @throws Exception
     * @throws HttpResponseException
     * @throws InvalidPaginationResponseException
     * @throws RequestTimeoutException
     * @throws UnsupportedPaginationResponseTypeException
     */
    public function list(
        ?FileUploadListRequest $fileUploadListRequest = null,
        ?PaginationRequest $paginationRequest = null
    ): AbstractPaginationResults {
        $paginationRequest = $paginationRequest ?? new PaginationRequest();

        $query = array_merge(
            $paginationRequest->toArray(),
            $fileUploadListRequest ? $fileUploadListRequest->toArray() : [],
        );

        $requestParameters = (new RequestParameters())
            ->setPath('file_uploads')
            ->setQuery($query)
            ->setMethod('GET');

        $rawData = $this->getClient()->request($requestParameters);

        return AbstractPaginationResults::fromRawData($rawData);
    }
}

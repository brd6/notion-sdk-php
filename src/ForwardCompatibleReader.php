<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp;

use Brd6\NotionSdkPhp\Endpoint\PagesPropertiesEndpoint;
use Brd6\NotionSdkPhp\Exception\InvalidPaginationResponseException;
use Brd6\NotionSdkPhp\Exception\InvalidResourceException;
use Brd6\NotionSdkPhp\Resource\Block\AbstractBlock;
use Brd6\NotionSdkPhp\Resource\Block\ForwardCompatibleBlockFactory;
use Brd6\NotionSdkPhp\Resource\Block\MeetingNotesQueryRequest;
use Brd6\NotionSdkPhp\Resource\Block\MeetingNotesQueryResults;
use Brd6\NotionSdkPhp\Resource\ForwardCompatiblePage;
use Brd6\NotionSdkPhp\Resource\Page;
use Brd6\NotionSdkPhp\Resource\Page\PropertyItem\ForwardCompatiblePropertyItemFactory;
use Brd6\NotionSdkPhp\Resource\Page\PropertyValue\AbstractPropertyValue;
use Brd6\NotionSdkPhp\Resource\Page\PropertyValue\ForwardCompatiblePropertyValueFactory;
use Brd6\NotionSdkPhp\Resource\Pagination\AbstractPaginationResults;
use Brd6\NotionSdkPhp\Resource\Pagination\BlockResults;
use Brd6\NotionSdkPhp\Resource\Pagination\PaginationRequest;
use Brd6\NotionSdkPhp\Resource\Pagination\PropertyItemResults;

use function array_map;

final class ForwardCompatibleReader
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function retrievePage(string $pageId): Page
    {
        $rawData = $this->client->request(
            (new RequestParameters())
                ->setPath("pages/$pageId")
                ->setMethod('GET'),
        );

        /** @var Page $page */
        $page = ForwardCompatiblePage::fromRawData($rawData);

        return $page;
    }

    public function retrieveBlock(string $blockId): AbstractBlock
    {
        $rawData = $this->client->request(
            (new RequestParameters())
                ->setPath("blocks/$blockId")
                ->setMethod('GET'),
        );

        return ForwardCompatibleBlockFactory::create($rawData);
    }

    public function listBlockChildren(
        string $blockId,
        ?PaginationRequest $paginationRequest = null
    ): AbstractPaginationResults {
        $paginationRequest = $paginationRequest ?? new PaginationRequest();
        $rawData = $this->client->request(
            (new RequestParameters())
                ->setPath("blocks/$blockId/children")
                ->setQuery($paginationRequest->toArray())
                ->setMethod('GET'),
        );

        if (!isset($rawData['object'], $rawData['type'])) {
            throw new InvalidPaginationResponseException();
        }

        if ($rawData['type'] !== 'block') {
            return AbstractPaginationResults::fromRawData($rawData);
        }

        $results = new BlockResults();
        $results->setRawData($rawData);
        $results->setResults(isset($rawData['results']) ? array_map(
            fn (array $result) => ForwardCompatibleBlockFactory::create($result),
            (array) $rawData['results'],
        ) : []);

        return $results;
    }

    public function queryMeetingNotes(
        ?MeetingNotesQueryRequest $queryRequest = null
    ): MeetingNotesQueryResults {
        $rawData = $this->client->request(
            (new RequestParameters())
                ->setPath('blocks/meeting_notes/query')
                ->setMethod('POST')
                ->setBody($queryRequest ? $queryRequest->toArray() : []),
        );

        return (new MeetingNotesQueryResults())
            ->setResults(isset($rawData['results']) ? array_map(
                fn (array $result) => ForwardCompatibleBlockFactory::create($result),
                (array) $rawData['results'],
            ) : [])
            ->setHasMore((bool) ($rawData['has_more'] ?? false));
    }

    /**
     * @return AbstractPropertyValue|AbstractPaginationResults
     */
    public function retrievePageProperty(
        string $pageId,
        string $propertyId,
        ?PaginationRequest $paginationRequest = null
    ) {
        $paginationRequest = $paginationRequest ?? new PaginationRequest();
        $rawData = $this->client->request(
            (new RequestParameters())
                ->setPath("pages/$pageId/properties/$propertyId")
                ->setQuery($paginationRequest->toArray())
                ->setMethod('GET'),
        );

        if (!isset($rawData['type'])) {
            throw new InvalidResourceException();
        }

        if ($rawData['type'] !== PagesPropertiesEndpoint::PROPERTY_ITEM_TYPE) {
            return ForwardCompatiblePropertyValueFactory::create($rawData);
        }

        if (!isset($rawData['object'])) {
            throw new InvalidPaginationResponseException();
        }

        $results = new PropertyItemResults();
        $results->setRawData($rawData);
        $results->setResults(isset($rawData['results']) ? array_map(
            fn (array $result) => ForwardCompatiblePropertyItemFactory::create($result),
            (array) $rawData['results'],
        ) : []);

        return $results;
    }
}

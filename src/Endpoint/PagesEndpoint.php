<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Endpoint;

use Brd6\NotionSdkPhp\Client;
use Brd6\NotionSdkPhp\Exception\ApiResponseException;
use Brd6\NotionSdkPhp\Exception\HttpResponseException;
use Brd6\NotionSdkPhp\Exception\InvalidResourceException;
use Brd6\NotionSdkPhp\Exception\InvalidResourceTypeException;
use Brd6\NotionSdkPhp\Exception\RequestTimeoutException;
use Brd6\NotionSdkPhp\RequestParameters;
use Brd6\NotionSdkPhp\Resource\AsyncTask;
use Brd6\NotionSdkPhp\Resource\Block\AbstractBlock;
use Brd6\NotionSdkPhp\Resource\Page;
use Brd6\NotionSdkPhp\Resource\Page\PageMarkdownRequest;
use Brd6\NotionSdkPhp\Resource\PageMarkdown;
use Http\Client\Exception;

use function array_map;
use function array_merge;

class PagesEndpoint extends AbstractEndpoint
{
    private PagesPropertiesEndpoint $pagesPropertiesEndpoint;

    public function __construct(Client $client)
    {
        parent::__construct($client);

        $this->pagesPropertiesEndpoint = new PagesPropertiesEndpoint($client);
    }

    /**
     * @param string $pageId
     *
     * @throws ApiResponseException
     * @throws HttpResponseException
     * @throws InvalidResourceException
     * @throws InvalidResourceTypeException
     * @throws RequestTimeoutException
     * @throws Exception
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

    /**
     * @param Page $page
     * @param array|AbstractBlock[] $children
     *
     * @throws ApiResponseException
     * @throws Exception
     * @throws HttpResponseException
     * @throws InvalidResourceException
     * @throws InvalidResourceTypeException
     * @throws RequestTimeoutException
     */
    public function create(Page $page, array $children = []): Page
    {
        $childrenData = array_map(fn (AbstractBlock $block) => $block->toArrayForCreate(), $children);

        $data = array_merge($this->normalizeTrashKey($page->toArrayForCreate()), ['children' => $childrenData]);

        $requestParameters = (new RequestParameters())
            ->setPath('pages')
            ->setMethod('POST')
            ->setBody($data);

        $rawData = $this->getClient()->request($requestParameters);

        /** @var Page $pageCreated */
        $pageCreated = Page::fromRawData($rawData);

        return $pageCreated;
    }

    /**
     * @param Page $page
     *
     * @throws ApiResponseException
     * @throws Exception
     * @throws HttpResponseException
     * @throws InvalidResourceException
     * @throws InvalidResourceTypeException
     * @throws RequestTimeoutException
     */
    public function update(Page $page): Page
    {
        $requestParameters = (new RequestParameters())
            ->setPath("pages/{$page->getId()}")
            ->setMethod('PATCH')
            ->setBody($this->normalizeTrashKey($page->toArrayForUpdate()));

        $rawData = $this->getClient()->request($requestParameters);

        /** @var Page $pageUpdated */
        $pageUpdated = Page::fromRawData($rawData);

        return $pageUpdated;
    }

    /**
     * Creates a page whose content is written as enhanced markdown; markdown and
     * block children are mutually exclusive at the API. Requires Notion-Version 2026-03-11.
     *
     * @throws ApiResponseException
     * @throws Exception
     * @throws HttpResponseException
     * @throws InvalidResourceException
     * @throws InvalidResourceTypeException
     * @throws RequestTimeoutException
     */
    public function createFromMarkdown(Page $page, string $markdown): Page
    {
        $rawData = $this->getClient()->request(
            $this->buildCreateFromMarkdownParameters($page, $markdown, false),
        );

        /** @var Page $pageCreated */
        $pageCreated = Page::fromRawData($rawData);

        return $pageCreated;
    }

    /**
     * Same call as createFromMarkdown() but handled asynchronously by Notion; poll the
     * returned task via asyncTasks()->retrieve(). Requires Notion-Version 2026-03-11.
     *
     * @throws ApiResponseException
     * @throws Exception
     * @throws HttpResponseException
     * @throws InvalidResourceException
     * @throws InvalidResourceTypeException
     * @throws RequestTimeoutException
     */
    public function createFromMarkdownAsync(Page $page, string $markdown): AsyncTask
    {
        $rawData = $this->getClient()->request(
            $this->buildCreateFromMarkdownParameters($page, $markdown, true),
        );

        /** @var AsyncTask $asyncTask */
        $asyncTask = AsyncTask::fromRawData($rawData);

        return $asyncTask;
    }

    /**
     * Requires Notion-Version 2026-03-11.
     *
     * @throws ApiResponseException
     * @throws Exception
     * @throws HttpResponseException
     * @throws InvalidResourceException
     * @throws InvalidResourceTypeException
     * @throws RequestTimeoutException
     */
    public function retrieveMarkdown(string $pageId, bool $includeTranscript = false): PageMarkdown
    {
        $requestParameters = (new RequestParameters())
            ->setPath("pages/$pageId/markdown")
            ->setMethod('GET');

        if ($includeTranscript) {
            $requestParameters->setQuery(['include_transcript' => 'true']);
        }

        $rawData = $this->getClient()->request($requestParameters);

        /** @var PageMarkdown $pageMarkdown */
        $pageMarkdown = PageMarkdown::fromRawData($rawData);

        return $pageMarkdown;
    }

    /**
     * Requires Notion-Version 2026-03-11.
     *
     * @throws ApiResponseException
     * @throws Exception
     * @throws HttpResponseException
     * @throws InvalidResourceException
     * @throws InvalidResourceTypeException
     * @throws RequestTimeoutException
     */
    public function updateMarkdown(string $pageId, PageMarkdownRequest $pageMarkdownRequest): PageMarkdown
    {
        $rawData = $this->getClient()->request(
            $this->buildUpdateMarkdownParameters($pageId, $pageMarkdownRequest, false),
        );

        /** @var PageMarkdown $pageMarkdown */
        $pageMarkdown = PageMarkdown::fromRawData($rawData);

        return $pageMarkdown;
    }

    /**
     * Same call as updateMarkdown() but handled asynchronously by Notion; poll the
     * returned task via asyncTasks()->retrieve(). Requires Notion-Version 2026-03-11.
     *
     * @throws ApiResponseException
     * @throws Exception
     * @throws HttpResponseException
     * @throws InvalidResourceException
     * @throws InvalidResourceTypeException
     * @throws RequestTimeoutException
     */
    public function updateMarkdownAsync(string $pageId, PageMarkdownRequest $pageMarkdownRequest): AsyncTask
    {
        $rawData = $this->getClient()->request(
            $this->buildUpdateMarkdownParameters($pageId, $pageMarkdownRequest, true),
        );

        /** @var AsyncTask $asyncTask */
        $asyncTask = AsyncTask::fromRawData($rawData);

        return $asyncTask;
    }

    private function buildCreateFromMarkdownParameters(
        Page $page,
        string $markdown,
        bool $allowAsync
    ): RequestParameters {
        $data = array_merge(
            $this->normalizeTrashKey($page->toArrayForCreate()),
            ['markdown' => $markdown],
        );

        if ($allowAsync) {
            $data['allow_async'] = true;
        }

        return (new RequestParameters())
            ->setPath('pages')
            ->setMethod('POST')
            ->setBody($data);
    }

    private function buildUpdateMarkdownParameters(
        string $pageId,
        PageMarkdownRequest $pageMarkdownRequest,
        bool $allowAsync
    ): RequestParameters {
        $data = $pageMarkdownRequest->toArray();

        if ($allowAsync) {
            $data['allow_async'] = true;
        }

        return (new RequestParameters())
            ->setPath("pages/$pageId/markdown")
            ->setMethod('PATCH')
            ->setBody($data);
    }

    public function properties(): PagesPropertiesEndpoint
    {
        return $this->pagesPropertiesEndpoint;
    }
}

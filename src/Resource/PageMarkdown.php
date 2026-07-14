<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource;

class PageMarkdown extends AbstractResource
{
    public const RESOURCE_TYPE = 'page_markdown';

    protected string $markdown = '';
    protected bool $truncated = false;
    protected array $unknownBlockIds = [];

    public function __construct()
    {
        parent::__construct();

        $this->object = self::RESOURCE_TYPE;
    }

    public static function getResourceType(): string
    {
        return self::RESOURCE_TYPE;
    }

    protected function initialize(): void
    {
        $rawData = $this->getRawData();

        $this->markdown = (string) ($rawData['markdown'] ?? '');
        $this->truncated = (bool) ($rawData['truncated'] ?? false);
        $this->unknownBlockIds = (array) ($rawData['unknown_block_ids'] ?? []);
    }

    public function getMarkdown(): string
    {
        return $this->markdown;
    }

    public function setMarkdown(string $markdown): self
    {
        $this->markdown = $markdown;

        return $this;
    }

    public function isTruncated(): bool
    {
        return $this->truncated;
    }

    public function setTruncated(bool $truncated): self
    {
        $this->truncated = $truncated;

        return $this;
    }

    /**
     * @return string[]|array
     */
    public function getUnknownBlockIds(): array
    {
        return $this->unknownBlockIds;
    }

    /**
     * @param string[]|array $unknownBlockIds
     */
    public function setUnknownBlockIds(array $unknownBlockIds): self
    {
        $this->unknownBlockIds = $unknownBlockIds;

        return $this;
    }
}

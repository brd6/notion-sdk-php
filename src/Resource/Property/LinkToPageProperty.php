<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Property;

class LinkToPageProperty extends AbstractProperty
{
    protected string $type = '';

    protected ?string $pageId = null;
    protected ?string $databaseId = null;

    public static function fromRawData(array $rawData): self
    {
        $property = new self();

        $property->type = (string) $rawData['type'];
        $property->pageId = isset($rawData['page_id']) ? (string) $rawData['page_id'] : null;
        $property->databaseId = isset($rawData['database_id']) ? (string) $rawData['database_id'] : null;

        return $property;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getPageId(): ?string
    {
        return $this->pageId;
    }

    public function setPageId(?string $pageId): self
    {
        $this->pageId = $pageId;

        return $this;
    }

    public function getDatabaseId(): ?string
    {
        return $this->databaseId;
    }

    public function setDatabaseId(?string $databaseId): self
    {
        $this->databaseId = $databaseId;

        return $this;
    }
}

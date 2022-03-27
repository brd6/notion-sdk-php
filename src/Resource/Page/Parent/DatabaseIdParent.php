<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Page\Parent;

use Brd6\NotionSdkPhp\Resource\Page\AbstractParentProperty;

class DatabaseIdParent extends AbstractParentProperty
{
    protected string $databaseId = '';

    protected function initialize(): void
    {
        $this->databaseId = (string) $this->getRawData()['database_id'];
    }

    public function getDatabaseId(): string
    {
        return $this->databaseId;
    }

    public function setDatabaseId(string $databaseId): self
    {
        $this->databaseId = $databaseId;

        return $this;
    }
}

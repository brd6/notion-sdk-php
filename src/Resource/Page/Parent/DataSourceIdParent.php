<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Page\Parent;

class DataSourceIdParent extends AbstractParentProperty
{
    protected string $dataSourceId = '';

    public function __construct()
    {
        $this->type = 'data_source_id';
    }

    protected function initialize(): void
    {
        $this->dataSourceId = (string) ($this->getRawData()['data_source_id'] ?? '');
    }

    public function getDataSourceId(): string
    {
        return $this->dataSourceId;
    }

    public function setDataSourceId(string $dataSourceId): self
    {
        $this->dataSourceId = $dataSourceId;

        return $this;
    }
}

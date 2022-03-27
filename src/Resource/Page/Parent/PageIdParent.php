<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Page\Parent;

use Brd6\NotionSdkPhp\Resource\Page\AbstractParentProperty;

class PageIdParent extends AbstractParentProperty
{
    protected string $pageId = '';

    protected function initialize(): void
    {
        $this->pageId = (string) $this->getRawData()['page_id'];
    }

    public function getPageId(): string
    {
        return $this->pageId;
    }

    public function setPageId(string $pageId): self
    {
        $this->pageId = $pageId;

        return $this;
    }
}

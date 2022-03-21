<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Block;

use Brd6\NotionSdkPhp\Resource\AbstractBlock;
use Brd6\NotionSdkPhp\Resource\Property\LinkToPageProperty;

class LinkToPageBlock extends AbstractBlock
{
    protected ?LinkToPageProperty $linkToPage = null;

    protected function initializeBlockProperty(): void
    {
        $data = (array) $this->getRawData()[$this->getType()];

        $this->linkToPage = LinkToPageProperty::fromRawData($data);
    }

    public function getLinkToPage(): ?LinkToPageProperty
    {
        return $this->linkToPage;
    }

    public function setLinkToPage(?LinkToPageProperty $linkToPage): self
    {
        $this->linkToPage = $linkToPage;

        return $this;
    }
}

<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Block;

use Brd6\NotionSdkPhp\Resource\AbstractBlock;
use Brd6\NotionSdkPhp\Resource\Property\LinkPreviewProperty;

class LinkPreviewBlock extends AbstractBlock
{
    protected ?LinkPreviewProperty $linkPreview = null;

    protected function initializeBlockProperty(): void
    {
        $data = (array) $this->getRawData()[$this->getType()];
        $this->linkPreview = LinkPreviewProperty::fromRawData($data);
    }

    public function getLinkPreview(): ?LinkPreviewProperty
    {
        return $this->linkPreview;
    }

    public function setLinkPreview(?LinkPreviewProperty $linkPreview): self
    {
        $this->linkPreview = $linkPreview;

        return $this;
    }
}

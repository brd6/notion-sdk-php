<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\RichText\Mention;

use Brd6\NotionSdkPhp\Resource\Property\LinkPreviewProperty;
use Brd6\NotionSdkPhp\Resource\RichText\AbstractMention;

class LinkPreviewMention extends AbstractMention
{
    private ?LinkPreviewProperty $linkPreview = null;

    protected function initialize(): void
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

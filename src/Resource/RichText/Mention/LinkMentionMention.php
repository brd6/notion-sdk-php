<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\RichText\Mention;

use Brd6\NotionSdkPhp\Resource\Property\LinkMentionProperty;
use Brd6\NotionSdkPhp\Resource\RichText\AbstractMention;

class LinkMentionMention extends AbstractMention
{
    protected ?LinkMentionProperty $linkMention = null;

    protected function initialize(): void
    {
        $data = (array) $this->getRawData()[$this->getType()];
        $this->linkMention = LinkMentionProperty::fromRawData($data);
    }

    public function getLinkMention(): ?LinkMentionProperty
    {
        return $this->linkMention;
    }

    public function setLinkMention(?LinkMentionProperty $linkMention): self
    {
        $this->linkMention = $linkMention;

        return $this;
    }
}

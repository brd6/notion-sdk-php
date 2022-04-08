<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Database\PropertyObject;

use Brd6\NotionSdkPhp\Resource\Database\PropertyConfiguration\RichTextPropertyConfiguration;

class RichTextPropertyObject extends AbstractPropertyObject
{
    protected ?RichTextPropertyConfiguration $richText = null;

    public function __construct()
    {
        $this->richText = new RichTextPropertyConfiguration();
    }

    protected function initialize(): void
    {
        $this->richText = isset($this->getRawData()['rich_text']) ?
            RichTextPropertyConfiguration::fromRawData((array) $this->getRawData()['rich_text']) :
            null;
    }

    public function getRichText(): ?RichTextPropertyConfiguration
    {
        return $this->richText;
    }

    public function setRichText(?RichTextPropertyConfiguration $richText): self
    {
        $this->richText = $richText;

        return $this;
    }
}

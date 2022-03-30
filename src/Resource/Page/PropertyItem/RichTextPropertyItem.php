<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Page\PropertyItem;

use Brd6\NotionSdkPhp\Resource\RichText\AbstractRichText;

class RichTextPropertyItem extends AbstractPropertyItem
{
    protected ?AbstractRichText $richText = null;

    protected function initialize(): void
    {
        $data = (array) $this->getRawData()[$this->getType()];
        $this->richText = AbstractRichText::fromRawData($data);
    }

    public function getRichText(): ?AbstractRichText
    {
        return $this->richText;
    }

    public function setRichText(AbstractRichText $richText): self
    {
        $this->richText = $richText;

        return $this;
    }
}

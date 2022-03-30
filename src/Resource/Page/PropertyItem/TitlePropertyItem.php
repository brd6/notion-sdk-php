<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Page\PropertyItem;

use Brd6\NotionSdkPhp\Resource\RichText\AbstractRichText;

class TitlePropertyItem extends AbstractPropertyItem
{
    protected ?AbstractRichText $title = null;

    protected function initialize(): void
    {
        $data = (array) $this->getRawData()[$this->getType()];

        $this->title = AbstractRichText::fromRawData($data);
    }

    public function getTitle(): ?AbstractRichText
    {
        return $this->title;
    }

    public function setTitle(AbstractRichText $title): self
    {
        $this->title = $title;

        return $this;
    }
}

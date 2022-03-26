<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\RichText\Mention;

use Brd6\NotionSdkPhp\Resource\Property\PartialPageProperty;
use Brd6\NotionSdkPhp\Resource\RichText\AbstractMention;

class PageMention extends AbstractMention
{
    protected ?PartialPageProperty $page = null;

    protected function initialize(): void
    {
        $data = (array) $this->getRawData()[$this->getType()];
        $this->page = PartialPageProperty::fromRawData($data);
    }

    public function getPage(): ?PartialPageProperty
    {
        return $this->page;
    }

    public function setPage(?PartialPageProperty $page): self
    {
        $this->page = $page;

        return $this;
    }
}

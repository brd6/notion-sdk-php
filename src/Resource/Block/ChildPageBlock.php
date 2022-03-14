<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Block;

use Brd6\NotionSdkPhp\Resource\AbstractBlock;
use Brd6\NotionSdkPhp\Resource\Property\ChildPageProperty;

class ChildPageBlock extends AbstractBlock
{
    protected ?ChildPageProperty $childPage = null;

    protected function initializeBlockProperty(): void
    {
        $data = (array) $this->getRawData()[$this->getType()];
        $this->childPage = ChildPageProperty::fromRawData($data);
    }

    public function getChildPage(): ?ChildPageProperty
    {
        return $this->childPage;
    }

    /**
     * @param ChildPageProperty $childPage
     *
     * @return ChildPageBlock
     */
    public function setChildPage(ChildPageProperty $childPage): self
    {
        $this->childPage = $childPage;

        return $this;
    }
}

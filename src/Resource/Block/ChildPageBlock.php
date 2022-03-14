<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Block;

use Brd6\NotionSdkPhp\Resource\Block;
use Brd6\NotionSdkPhp\Resource\Property\ChildPageProperty;

class ChildPageBlock extends Block
{
    protected ChildPageProperty $childPage;

    protected function initializeBlockProperty(): void
    {
        $this->childPage = ChildPageProperty::fromData($this->getResponseData()[$this->getType()]);
    }

    public function getChildPage(): ChildPageProperty
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

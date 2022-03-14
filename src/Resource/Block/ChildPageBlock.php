<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Block;

use Brd6\NotionSdkPhp\Resource\Block;
use Brd6\NotionSdkPhp\Resource\Property\ChildPageProperty;

class ChildPageBlock extends Block
{
    protected ?ChildPageProperty $childPage = null;

    protected function initializeBlockProperty(): void
    {
        $data = (array) $this->getResponseData()[$this->getType()];
        $this->childPage = ChildPageProperty::fromData($data);
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

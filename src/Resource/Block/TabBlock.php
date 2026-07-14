<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Block;

use Brd6\NotionSdkPhp\Resource\Property\TabProperty;

class TabBlock extends AbstractBlock
{
    protected ?TabProperty $tab = null;

    protected function initializeBlockProperty(): void
    {
        $data = (array) $this->getRawData()[$this->getType()];
        $this->tab = TabProperty::fromRawData($data);
    }

    public function getTab(): ?TabProperty
    {
        return $this->tab;
    }

    public function setTab(?TabProperty $tab): self
    {
        $this->tab = $tab;

        return $this;
    }
}

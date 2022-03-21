<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Block;

use Brd6\NotionSdkPhp\Resource\AbstractBlock;
use Brd6\NotionSdkPhp\Resource\Property\DividerProperty;

class DividerBlock extends AbstractBlock
{
    protected ?DividerProperty $divider = null;

    protected function initializeBlockProperty(): void
    {
        $data = (array) $this->getRawData()[$this->getType()];
        $this->divider = DividerProperty::fromRawData($data);
    }

    public function getDivider(): ?DividerProperty
    {
        return $this->divider;
    }

    public function setDivider(?DividerProperty $divider): DividerBlock
    {
        $this->divider = $divider;

        return $this;
    }
}

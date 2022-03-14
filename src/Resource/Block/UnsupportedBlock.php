<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Block;

use Brd6\NotionSdkPhp\Resource\AbstractBlock;

class UnsupportedBlock extends AbstractBlock
{
    protected function initialize(): void
    {
        $this->type = (string) $this->getRawData()['type'];
    }

    protected function initializeBlockProperty(): void
    {
    }
}

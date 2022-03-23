<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Block;

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

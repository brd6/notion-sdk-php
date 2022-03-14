<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Block;

use Brd6\NotionSdkPhp\Resource\Block;

class UnsupportedBlock extends Block
{
    protected function initialize(): void
    {
        $this->type = (string) $this->getResponseData()['type'];
    }

    protected function initializeBlockProperty(): void
    {
    }
}

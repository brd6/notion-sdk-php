<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Block;

class ColumnListBlock extends AbstractBlock
{
    protected function initializeBlockProperty(): void
    {
        // Column list blocks don't have specific properties beyond children
        // Children are now handled by AbstractBlock
    }
}

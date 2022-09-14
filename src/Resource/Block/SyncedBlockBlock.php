<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Block;

use Brd6\NotionSdkPhp\Resource\Property\SyncedBlockProperty;

class SyncedBlockBlock extends AbstractBlock
{
    protected ?SyncedBlockProperty $syncedBlock = null;

    protected function initializeBlockProperty(): void
    {
        $data = (array) $this->getRawData()[$this->getType()];

        $this->syncedBlock = SyncedBlockProperty::fromRawData($data);
    }

    public function getSyncedBlock(): ?SyncedBlockProperty
    {
        return $this->syncedBlock;
    }

    public function setSyncedBlock(?SyncedBlockProperty $syncedBlock): self
    {
        $this->syncedBlock = $syncedBlock;

        return $this;
    }
}

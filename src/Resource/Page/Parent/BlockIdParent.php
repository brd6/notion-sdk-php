<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Page\Parent;

class BlockIdParent extends AbstractParentProperty
{
    protected string $blockId = '';

    public function __construct()
    {
        $this->type = 'block_id';
    }

    protected function initialize(): void
    {
        $this->blockId = (string) $this->getRawData()['block_id'];
    }

    public function getBlockId(): string
    {
        return $this->blockId;
    }

    public function setBlockId(string $blockId): self
    {
        $this->blockId = $blockId;

        return $this;
    }
}

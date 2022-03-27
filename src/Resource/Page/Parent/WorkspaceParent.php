<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Page\Parent;

use Brd6\NotionSdkPhp\Resource\Page\AbstractParentProperty;

class WorkspaceParent extends AbstractParentProperty
{
    protected bool $workspace = false;

    protected function initialize(): void
    {
        $this->workspace = (bool) $this->getRawData()['workspace'];
    }

    public function isWorkspace(): bool
    {
        return $this->workspace;
    }

    public function setWorkspace(bool $workspace): self
    {
        $this->workspace = $workspace;

        return $this;
    }
}

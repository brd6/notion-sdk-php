<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Page\Parent;

class AgentIdParent extends AbstractParentProperty
{
    protected string $agentId = '';

    public function __construct()
    {
        $this->type = 'agent_id';
    }

    protected function initialize(): void
    {
        $this->agentId = (string) $this->getRawData()['agent_id'];
    }

    public function getAgentId(): string
    {
        return $this->agentId;
    }

    public function setAgentId(string $agentId): self
    {
        $this->agentId = $agentId;

        return $this;
    }
}

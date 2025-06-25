<?php

declare(strict_types=1);

namespace NotionOAuth;

use League\OAuth2\Client\Provider\GenericResourceOwner;

class NotionResourceOwner extends GenericResourceOwner
{
    private function getResponseValue(string $key): mixed
    {
        return $this->response[$key] ?? null;
    }

    public function getId()
    {
        return $this->resourceOwnerId;
    }

    public function getWorkspaceId(): ?string
    {
        return $this->getResponseValue('workspace_id');
    }

    public function getWorkspaceName(): ?string
    {
        return $this->getResponseValue('workspace_name');
    }

    public function getWorkspaceIcon(): ?string
    {
        return $this->getResponseValue('workspace_icon');
    }

    public function getBotId(): ?string
    {
        return $this->getResponseValue('bot_id');
    }

    public function getAccessToken(): ?string
    {
        return $this->getResponseValue('access_token');
    }
} 
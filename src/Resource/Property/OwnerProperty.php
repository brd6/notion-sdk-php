<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Property;

use Brd6\NotionSdkPhp\Resource\AbstractProperty;
use Brd6\NotionSdkPhp\Resource\AbstractUser;
use Brd6\NotionSdkPhp\Resource\UserInterface;

use function array_key_exists;

class OwnerProperty extends AbstractProperty
{
    protected string $type = '';
    protected ?bool $workspace = true;
    protected ?UserInterface $user = null;

    public static function fromRawData(array $rawData): self
    {
        $property = new self();

        $property->type = (string) $rawData['type'];
        $property->workspace = array_key_exists('workspace', $rawData) ?
            (bool) $rawData['workspace'] :
            null;
        $property->user = isset($rawData['user']) ?
            AbstractUser::fromRawData((array) $rawData['user']) :
            null;

        return $property;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getWorkspace(): ?bool
    {
        return $this->workspace;
    }

    public function setWorkspace(?bool $workspace): self
    {
        $this->workspace = $workspace;

        return $this;
    }

    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    public function setUser(?UserInterface $user): self
    {
        $this->user = $user;

        return $this;
    }
}

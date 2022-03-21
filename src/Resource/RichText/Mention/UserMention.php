<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\RichText\Mention;

use Brd6\NotionSdkPhp\Resource\AbstractUser;
use Brd6\NotionSdkPhp\Resource\RichText\AbstractMention;
use Brd6\NotionSdkPhp\Resource\UserInterface;

class UserMention extends AbstractMention
{
    private ?UserInterface $user = null;

    protected function initialize(): void
    {
        $data = (array) $this->getRawData()[$this->getType()];
        $this->user = AbstractUser::fromRawData($data);
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

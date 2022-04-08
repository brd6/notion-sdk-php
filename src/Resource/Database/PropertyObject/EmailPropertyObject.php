<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Database\PropertyObject;

use Brd6\NotionSdkPhp\Resource\Database\PropertyConfiguration\EmailPropertyConfiguration;

class EmailPropertyObject extends AbstractPropertyObject
{
    protected ?EmailPropertyConfiguration $email = null;

    public function __construct()
    {
        $this->email = new EmailPropertyConfiguration();
    }

    protected function initialize(): void
    {
        $this->email = isset($this->getRawData()['email']) ?
            EmailPropertyConfiguration::fromRawData((array) $this->getRawData()['email']) :
            null;
    }

    public function getEmail(): ?EmailPropertyConfiguration
    {
        return $this->email;
    }

    public function setEmail(?EmailPropertyConfiguration $email): self
    {
        $this->email = $email;

        return $this;
    }
}

<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Page\PropertyValue;

use Brd6\NotionSdkPhp\Resource\Page\AbstractPropertyValue;

class EmailPropertyValue extends AbstractPropertyValue
{
    protected string $email = '';

    protected function initialize(): void
    {
        $this->email = (string) ($this->getRawData()[$this->getType()] ?? '');
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }
}

<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Page\PropertyValue;

use Brd6\NotionSdkPhp\Resource\Page\AbstractPropertyValue;

class PhoneNumberPropertyValue extends AbstractPropertyValue
{
    protected string $phoneNumber = '';

    protected function initialize(): void
    {
        $this->phoneNumber = (string) ($this->getRawData()[$this->getType()] ?? '');
    }

    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }
}

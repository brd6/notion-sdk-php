<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Database\PropertyObject;

use Brd6\NotionSdkPhp\Resource\Database\PropertyConfiguration\PhoneNumberPropertyConfiguration;

class PhoneNumberPropertyObject extends AbstractPropertyObject
{
    protected ?PhoneNumberPropertyConfiguration $phoneNumber = null;

    public function __construct()
    {
        $this->phoneNumber = new PhoneNumberPropertyConfiguration();
    }

    protected function initialize(): void
    {
        $this->phoneNumber = isset($this->getRawData()['phone_number']) ?
            PhoneNumberPropertyConfiguration::fromRawData((array) $this->getRawData()['phone_number']) :
            null;
    }

    public function getPhoneNumber(): ?PhoneNumberPropertyConfiguration
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?PhoneNumberPropertyConfiguration $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }
}

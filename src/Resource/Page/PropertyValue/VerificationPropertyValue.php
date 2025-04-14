<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Page\PropertyValue;

use Brd6\NotionSdkPhp\Resource\Property\DateProperty;
use Brd6\NotionSdkPhp\Resource\User\AbstractUser;

class VerificationPropertyValue extends AbstractPropertyValue
{
    protected string $state = 'unverified';
    protected ?AbstractUser $verifiedBy = null;
    protected ?DateProperty $date = null;

    protected function initialize(): void
    {
        $data = (array) $this->getRawData()[$this->getType()];
        $this->state = (string) $data['state'];

        if (isset($data['verified_by']) && $data['verified_by'] !== null) {
            $this->verifiedBy = AbstractUser::fromRawData((array) $data['verified_by']);
        }

        if (isset($data['date']) && $data['date'] !== null) {
            $this->date = DateProperty::fromRawData((array) $data['date']);
        }
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function setState(string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getVerifiedBy(): ?AbstractUser
    {
        return $this->verifiedBy;
    }

    public function setVerifiedBy(?AbstractUser $verifiedBy): self
    {
        $this->verifiedBy = $verifiedBy;

        return $this;
    }

    public function getDate(): ?DateProperty
    {
        return $this->date;
    }

    public function setDate(?DateProperty $date): self
    {
        $this->date = $date;

        return $this;
    }
}

<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Database\PropertyObject;

use Brd6\NotionSdkPhp\Resource\Database\PropertyConfiguration\VerificationPropertyConfiguration;

class VerificationPropertyObject extends AbstractPropertyObject
{
    protected ?VerificationPropertyConfiguration $verification = null;

    public function __construct()
    {
        $this->verification = new VerificationPropertyConfiguration();
    }

    protected function initialize(): void
    {
        $this->verification = isset($this->getRawData()['verification']) ?
            VerificationPropertyConfiguration::fromRawData((array) $this->getRawData()['verification']) :
            null;
    }

    public function getVerification(): ?VerificationPropertyConfiguration
    {
        return $this->verification;
    }

    public function setVerification(?VerificationPropertyConfiguration $verification): self
    {
        $this->verification = $verification;

        return $this;
    }
}

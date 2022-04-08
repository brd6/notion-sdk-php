<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Database\PropertyObject;

use Brd6\NotionSdkPhp\Resource\Database\PropertyConfiguration\CheckboxPropertyConfiguration;

class CheckboxPropertyObject extends AbstractPropertyObject
{
    protected ?CheckboxPropertyConfiguration $checkbox = null;

    public function __construct()
    {
        $this->checkbox = new CheckboxPropertyConfiguration();
    }

    protected function initialize(): void
    {
        $this->checkbox = isset($this->getRawData()['checkbox']) ?
            CheckboxPropertyConfiguration::fromRawData((array) $this->getRawData()['checkbox']) :
            null;
    }

    public function getCheckbox(): ?CheckboxPropertyConfiguration
    {
        return $this->checkbox;
    }

    public function setCheckbox(?CheckboxPropertyConfiguration $checkbox): self
    {
        $this->checkbox = $checkbox;

        return $this;
    }
}

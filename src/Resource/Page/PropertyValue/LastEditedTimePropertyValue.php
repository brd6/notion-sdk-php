<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Page\PropertyValue;

use Brd6\NotionSdkPhp\Resource\Page\AbstractPropertyValue;
use DateTimeImmutable;

class LastEditedTimePropertyValue extends AbstractPropertyValue
{
    protected ?DateTimeImmutable $lastEditedTime = null;

    protected function initialize(): void
    {
        $this->lastEditedTime = new DateTimeImmutable((string) $this->getRawData()[$this->getType()]);
    }

    public function getLastEditedTime(): ?DateTimeImmutable
    {
        return $this->lastEditedTime;
    }

    public function setLastEditedTime(?DateTimeImmutable $lastEditedTime): self
    {
        $this->lastEditedTime = $lastEditedTime;

        return $this;
    }
}

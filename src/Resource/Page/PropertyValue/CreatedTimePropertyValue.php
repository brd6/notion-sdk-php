<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Page\PropertyValue;

use Brd6\NotionSdkPhp\Resource\Page\AbstractPropertyValue;
use DateTimeImmutable;

class CreatedTimePropertyValue extends AbstractPropertyValue
{
    protected ?DateTimeImmutable $createdTime = null;

    protected function initialize(): void
    {
        $this->createdTime = new DateTimeImmutable((string) $this->getRawData()[$this->getType()]);
    }

    public function getCreatedTime(): ?DateTimeImmutable
    {
        return $this->createdTime;
    }

    public function setCreatedTime(?DateTimeImmutable $createdTime): self
    {
        $this->createdTime = $createdTime;

        return $this;
    }
}

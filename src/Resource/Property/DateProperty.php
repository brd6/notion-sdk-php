<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Property;

use DateTimeImmutable;

class DateProperty extends AbstractProperty
{
    protected ?DateTimeImmutable $start = null;
    protected ?DateTimeImmutable $end = null;
    protected ?string $timeZone = null;

    public static function fromRawData(array $rawData): self
    {
        $property = new self();

        $property->start = isset($rawData['start']) ?
            new DateTimeImmutable((string)$rawData['start']) :
            null;
        $property->end = isset($rawData['end']) ?
            new DateTimeImmutable((string)$rawData['end']) :
            null;
        $property->timeZone = isset($rawData['time_zone']) ? (string)$rawData['time_zone'] : null;

        return $property;
    }

    public function getStart(): ?DateTimeImmutable
    {
        return $this->start;
    }

    public function setStart(?DateTimeImmutable $start): self
    {
        $this->start = $start;

        return $this;
    }

    public function getEnd(): ?DateTimeImmutable
    {
        return $this->end;
    }

    public function setEnd(?DateTimeImmutable $end): self
    {
        $this->end = $end;

        return $this;
    }

    public function getTimeZone(): ?string
    {
        return $this->timeZone;
    }

    public function setTimeZone(?string $timeZone): self
    {
        $this->timeZone = $timeZone;

        return $this;
    }

    public function jsonSerialize(): array
    {
        $r = array();

        if (!empty($this->getEnd())) {
            $r['end'] = $this->end->format(DateTimeImmutable::ISO8601);
        }
        if (!empty($this->getStart())) {
            $r['start'] = $this->start->format(DateTimeImmutable::ISO8601);
        }
        return $r;
    }
}

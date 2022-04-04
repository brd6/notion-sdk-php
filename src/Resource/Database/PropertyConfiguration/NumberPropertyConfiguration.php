<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Database\PropertyConfiguration;

use Brd6\NotionSdkPhp\Resource\Property\AbstractProperty;

class NumberPropertyConfiguration extends AbstractProperty
{
    protected string $format = '';

    public static function fromRawData(array $rawData): self
    {
        $property = new self();

        $property->format = (string) $rawData['format'];

        return $property;
    }

    public function getFormat(): string
    {
        return $this->format;
    }

    public function setFormat(string $format): self
    {
        $this->format = $format;

        return $this;
    }
}

<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Database\PropertyObject;

use Brd6\NotionSdkPhp\Exception\InvalidPropertyConfigurationException;
use Brd6\NotionSdkPhp\Exception\UnsupportedPropertyConfigurationException;
use Brd6\NotionSdkPhp\Resource\Database\PropertyConfiguration\NumberPropertyConfiguration;

class NumberPropertyObject extends AbstractPropertyObject
{
    protected ?NumberPropertyConfiguration $number = null;

    /**
     * @throws UnsupportedPropertyConfigurationException
     * @throws InvalidPropertyConfigurationException
     */
    protected function initialize(): void
    {
        $data = (array) $this->getRawData()[$this->getType()];
        $this->number = NumberPropertyConfiguration::fromRawData($data);
    }

    public function getNumber(): ?NumberPropertyConfiguration
    {
        return $this->number;
    }

    public function setNumber(NumberPropertyConfiguration $number): self
    {
        $this->number = $number;

        return $this;
    }
}

<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Property;

class PersonProperty extends AbstractProperty
{
    protected string $email = '';

    public static function fromRawData(array $rawData): self
    {
        $property = new self();

        $property->email = (string) $rawData['email'];

        return $property;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }
}

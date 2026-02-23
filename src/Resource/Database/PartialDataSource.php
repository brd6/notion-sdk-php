<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Database;

use Brd6\NotionSdkPhp\Resource\Property\AbstractProperty;

class PartialDataSource extends AbstractProperty
{
    protected string $id = '';
    protected string $name = '';

    public static function fromRawData(array $rawData): self
    {
        $property = new self();

        $property->id = (string) ($rawData['id'] ?? '');
        $property->name = (string) ($rawData['name'] ?? '');

        return $property;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}

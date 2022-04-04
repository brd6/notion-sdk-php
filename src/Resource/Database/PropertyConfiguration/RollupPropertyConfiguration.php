<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Database\PropertyConfiguration;

use Brd6\NotionSdkPhp\Resource\Property\AbstractProperty;

class RollupPropertyConfiguration extends AbstractProperty
{
    protected string $relationPropertyName = '';
    protected string $relationPropertyId = '';
    protected string $rollupPropertyName = '';
    protected string $rollupPropertyId = '';
    protected string $function = '';

    public static function fromRawData(array $rawData): self
    {
        $property = new self();

        $property->relationPropertyName = (string) $rawData['relation_property_name'];
        $property->relationPropertyId = (string) $rawData['relation_property_id'];
        $property->rollupPropertyName = (string) $rawData['rollup_property_name'];
        $property->rollupPropertyId = (string) $rawData['rollup_property_id'];
        $property->function = (string) $rawData['function'];

        return $property;
    }

    public function getRelationPropertyName(): string
    {
        return $this->relationPropertyName;
    }

    public function setRelationPropertyName(string $relationPropertyName): self
    {
        $this->relationPropertyName = $relationPropertyName;

        return $this;
    }

    public function getRelationPropertyId(): string
    {
        return $this->relationPropertyId;
    }

    public function setRelationPropertyId(string $relationPropertyId): self
    {
        $this->relationPropertyId = $relationPropertyId;

        return $this;
    }

    public function getRollupPropertyName(): string
    {
        return $this->rollupPropertyName;
    }

    public function setRollupPropertyName(string $rollupPropertyName): self
    {
        $this->rollupPropertyName = $rollupPropertyName;

        return $this;
    }

    public function getRollupPropertyId(): string
    {
        return $this->rollupPropertyId;
    }

    public function setRollupPropertyId(string $rollupPropertyId): self
    {
        $this->rollupPropertyId = $rollupPropertyId;

        return $this;
    }

    public function getFunction(): string
    {
        return $this->function;
    }

    public function setFunction(string $function): self
    {
        $this->function = $function;

        return $this;
    }
}

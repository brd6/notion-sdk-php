<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Database\PropertyConfiguration;

use Brd6\NotionSdkPhp\Resource\Property\AbstractProperty;

class RelationPropertyConfiguration extends AbstractProperty
{
    protected string $databaseId = '';
    protected ?string $syncedPropertyName = null;
    protected ?string $syncedPropertyId = null;

    public static function fromRawData(array $rawData): self
    {
        $property = new self();

        $property->databaseId = (string) $rawData['database_id'];
        $property->syncedPropertyName = isset($rawData['synced_property_name']) ?
            (string) $rawData['synced_property_name'] :
            null;
        $property->syncedPropertyId = isset($rawData['synced_property_id']) ?
            (string) $rawData['synced_property_id'] :
            null;

        return $property;
    }

    public function getDatabaseId(): string
    {
        return $this->databaseId;
    }

    public function setDatabaseId(string $databaseId): self
    {
        $this->databaseId = $databaseId;

        return $this;
    }

    public function getSyncedPropertyName(): ?string
    {
        return $this->syncedPropertyName;
    }

    public function setSyncedPropertyName(?string $syncedPropertyName): self
    {
        $this->syncedPropertyName = $syncedPropertyName;

        return $this;
    }

    public function getSyncedPropertyId(): ?string
    {
        return $this->syncedPropertyId;
    }

    public function setSyncedPropertyId(?string $syncedPropertyId): self
    {
        $this->syncedPropertyId = $syncedPropertyId;

        return $this;
    }
}

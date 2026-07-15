<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Database\PropertyConfiguration;

use Brd6\NotionSdkPhp\Resource\Property\AbstractProperty;

use function in_array;

class RelationPropertyConfiguration extends AbstractProperty
{
    public const TYPE_SINGLE_PROPERTY = 'single_property';
    public const TYPE_DUAL_PROPERTY = 'dual_property';

    protected string $databaseId = '';
    protected string $dataSourceId = '';
    protected ?string $type = null;
    protected ?array $singleProperty = null;
    protected ?array $dualProperty = null;
    protected ?string $syncedPropertyName = null;
    protected ?string $syncedPropertyId = null;

    public static function fromRawData(array $rawData): self
    {
        $property = new self();

        $property->databaseId = (string) ($rawData['database_id'] ?? '');
        $property->dataSourceId = (string) ($rawData['data_source_id'] ?? '');
        $property->singleProperty = ($rawData['single_property'] ?? null) === [] ? [] : null;
        $property->dualProperty = ($rawData['dual_property'] ?? null) === [] ? [] : null;
        $property->type = $property->resolveType($rawData);
        $dualProperty = (array) ($rawData['dual_property'] ?? []);
        $property->syncedPropertyName = isset($rawData['synced_property_name']) ?
            (string) $rawData['synced_property_name'] :
            ((isset($dualProperty['synced_property_name']) ? (string) $dualProperty['synced_property_name'] : null));
        $property->syncedPropertyId = isset($rawData['synced_property_id']) ?
            (string) $rawData['synced_property_id'] :
            ((isset($dualProperty['synced_property_id']) ? (string) $dualProperty['synced_property_id'] : null));
        $property->syncedPropertyName = $property->syncedPropertyName !== '' ?
            $property->syncedPropertyName :
            null;
        $property->syncedPropertyId = $property->syncedPropertyId !== '' ?
            $property->syncedPropertyId :
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

    public function getDataSourceId(): string
    {
        return $this->dataSourceId;
    }

    public function setDataSourceId(string $dataSourceId): self
    {
        $this->dataSourceId = $dataSourceId;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getSingleProperty(): ?array
    {
        return $this->singleProperty;
    }

    public function getDualProperty(): ?array
    {
        return $this->dualProperty;
    }

    public function setSinglePropertyRelation(): self
    {
        $this->type = self::TYPE_SINGLE_PROPERTY;
        $this->singleProperty = [];
        $this->dualProperty = null;

        return $this;
    }

    public function setDualPropertyRelation(): self
    {
        $this->type = self::TYPE_DUAL_PROPERTY;
        $this->dualProperty = [];
        $this->singleProperty = null;

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

    private function resolveType(array $rawData): ?string
    {
        if (isset($rawData['type'])) {
            return (string) $rawData['type'];
        }

        if (isset($rawData['single_property'])) {
            return self::TYPE_SINGLE_PROPERTY;
        }

        if (isset($rawData['dual_property'])) {
            return self::TYPE_DUAL_PROPERTY;
        }

        return null;
    }

    private function hasEmptyRelationMarker(): bool
    {
        return $this->singleProperty === [] || $this->dualProperty === [];
    }

    /**
     * @param mixed $value
     */
    protected function canBeSerialized($value, string $key): bool
    {
        if (in_array($key, ['singleProperty', 'dualProperty'], true)) {
            return $value === [];
        }

        if ($key === 'type') {
            return $this->hasEmptyRelationMarker();
        }

        return parent::canBeSerialized($value, $key);
    }
}

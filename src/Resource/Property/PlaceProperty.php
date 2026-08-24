<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Property;

use Brd6\NotionSdkPhp\Exception\InvalidPropertyValueException;

use function is_numeric;

class PlaceProperty extends AbstractProperty
{
    protected ?float $lat = null;
    protected ?float $lon = null;
    protected ?string $name = null;
    protected ?string $address = null;
    protected ?string $awsPlaceId = null;
    protected ?string $googlePlaceId = null;

    public static function fromRawData(array $rawData): self
    {
        self::validateCoordinates($rawData['lat'] ?? null, $rawData['lon'] ?? null);

        $property = new self();

        $property->lat = isset($rawData['lat']) ? (float) $rawData['lat'] : null;
        $property->lon = isset($rawData['lon']) ? (float) $rawData['lon'] : null;
        $property->name = isset($rawData['name']) ? (string) $rawData['name'] : null;
        $property->address = isset($rawData['address']) ? (string) $rawData['address'] : null;
        $property->awsPlaceId = isset($rawData['aws_place_id']) ? (string) $rawData['aws_place_id'] : null;
        $property->googlePlaceId = isset($rawData['google_place_id']) ? (string) $rawData['google_place_id'] : null;

        return $property;
    }

    public function jsonSerialize(): array
    {
        self::validateCoordinates($this->lat, $this->lon);

        return parent::jsonSerialize();
    }

    /**
     * @param mixed $lat
     * @param mixed $lon
     */
    private static function validateCoordinates($lat, $lon): void
    {
        if (!is_numeric($lat) || !is_numeric($lon)) {
            throw new InvalidPropertyValueException();
        }
    }

    public function getLat(): ?float
    {
        return $this->lat;
    }

    public function setLat(?float $lat): self
    {
        $this->lat = $lat;

        return $this;
    }

    public function getLon(): ?float
    {
        return $this->lon;
    }

    public function setLon(?float $lon): self
    {
        $this->lon = $lon;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getAwsPlaceId(): ?string
    {
        return $this->awsPlaceId;
    }

    public function setAwsPlaceId(?string $awsPlaceId): self
    {
        $this->awsPlaceId = $awsPlaceId;

        return $this;
    }

    public function getGooglePlaceId(): ?string
    {
        return $this->googlePlaceId;
    }

    public function setGooglePlaceId(?string $googlePlaceId): self
    {
        $this->googlePlaceId = $googlePlaceId;

        return $this;
    }
}

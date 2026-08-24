<?php

declare(strict_types=1);

namespace Brd6\Test\NotionSdkPhp\Resource\Page\PropertyValue;

use Brd6\NotionSdkPhp\Resource\Page;
use Brd6\NotionSdkPhp\Resource\Page\PropertyValue\PlacePropertyValue;
use Brd6\NotionSdkPhp\Resource\Property\PlaceProperty;
use PHPUnit\Framework\TestCase;

use function file_get_contents;
use function json_decode;

class PlacePropertyValueTest extends TestCase
{
    public function testNullPlaceProperty(): void
    {
        $rawData = $this->getPageRawData();
        $rawData['properties']['Location'] = [
            'id' => 'place-id',
            'type' => 'place',
            'place' => null,
        ];

        /** @var Page $page */
        $page = Page::fromRawData($rawData);

        $place = $page->getProperties()['Location'];

        $this->assertInstanceOf(PlacePropertyValue::class, $place);
        $this->assertSame('place', $place->getType());
        $this->assertNull($place->getPlace());
    }

    public function testPopulatedPlaceProperty(): void
    {
        $rawData = $this->getPageRawData();
        $rawData['properties']['Location'] = [
            'id' => 'place-id',
            'type' => 'place',
            'place' => [
                'lat' => 48.8566,
                'lon' => 2.3522,
                'name' => 'Paris',
                'address' => 'Paris, France',
                'aws_place_id' => 'aws-place-id',
                'google_place_id' => 'google-place-id',
            ],
        ];

        /** @var Page $page */
        $page = Page::fromRawData($rawData);

        /** @var PlacePropertyValue $placeValue */
        $placeValue = $page->getProperties()['Location'];
        $place = $placeValue->getPlace();

        $this->assertInstanceOf(PlaceProperty::class, $place);
        $this->assertSame(48.8566, $place->getLat());
        $this->assertSame(2.3522, $place->getLon());
        $this->assertSame('Paris', $place->getName());
        $this->assertSame('Paris, France', $place->getAddress());
        $this->assertSame('aws-place-id', $place->getAwsPlaceId());
        $this->assertSame('google-place-id', $place->getGooglePlaceId());
    }

    public function testPopulatedPlaceSerialization(): void
    {
        $place = (new PlaceProperty())
            ->setLat(48.8566)
            ->setLon(2.3522)
            ->setName('Paris')
            ->setAddress('Paris, France')
            ->setAwsPlaceId('aws-place-id')
            ->setGooglePlaceId('google-place-id');
        $page = (new Page())->setProperties([
            'Location' => (new PlacePropertyValue())->setPlace($place),
        ]);
        $expected = [
            'lat' => 48.8566,
            'lon' => 2.3522,
            'name' => 'Paris',
            'address' => 'Paris, France',
            'aws_place_id' => 'aws-place-id',
            'google_place_id' => 'google-place-id',
        ];

        $this->assertSame($expected, $page->toArrayForCreate()['properties']['Location']['place']);
        $this->assertSame($expected, $page->toArrayForUpdate()['properties']['Location']['place']);
    }

    private function getPageRawData(): array
    {
        return (array) json_decode(
            (string) file_get_contents('tests/Fixtures/client_pages_retrieve_page_200.json'),
            true,
        );
    }
}

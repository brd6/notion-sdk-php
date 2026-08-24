<?php

declare(strict_types=1);

namespace Brd6\Test\NotionSdkPhp\Resource\Page\PropertyValue;

use Brd6\NotionSdkPhp\Exception\InvalidPropertyValueException;
use Brd6\NotionSdkPhp\Resource\Page;
use Brd6\NotionSdkPhp\Resource\Page\PropertyValue\PlacePropertyValue;
use Brd6\NotionSdkPhp\Resource\Property\PlaceProperty;
use PHPUnit\Framework\TestCase;

use function file_get_contents;
use function json_decode;

use const INF;
use const NAN;

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

    public function testExplicitNullPlaceSerialization(): void
    {
        $page = (new Page())->setProperties([
            'Location' => (new PlacePropertyValue())->setPlace(null),
        ]);

        $expected = ['place' => null];

        $this->assertSame($expected, $page->toArrayForCreate()['properties']['Location']);
        $this->assertSame($expected, $page->toArrayForUpdate()['properties']['Location']);
    }

    public function testHydratedNullPlaceIsOmittedFromUpdateUntilExplicitlySet(): void
    {
        $rawData = $this->getPageRawData();
        $rawData['properties']['Location'] = [
            'id' => 'place-id',
            'type' => 'place',
            'place' => null,
        ];

        /** @var Page $page */
        $page = Page::fromRawData($rawData);

        $this->assertArrayNotHasKey('Location', $page->toArrayForUpdate()['properties']);

        /** @var PlacePropertyValue $placeValue */
        $placeValue = $page->getProperties()['Location'];
        $placeValue->setPlace(null);

        $this->assertSame(
            ['type' => 'place', 'id' => 'place-id', 'place' => null],
            $page->toArrayForUpdate()['properties']['Location'],
        );
    }

    /**
     * @dataProvider invalidPlaceDataProvider
     */
    public function testInvalidPopulatedPlaceResponse(array $rawPlace): void
    {
        $this->expectException(InvalidPropertyValueException::class);

        PlaceProperty::fromRawData($rawPlace);
    }

    public function invalidPlaceDataProvider(): array
    {
        return [
            'missing latitude' => [['lon' => 2.3522]],
            'missing longitude' => [['lat' => 48.8566]],
            'invalid latitude' => [['lat' => 'north', 'lon' => 2.3522]],
            'invalid longitude' => [['lat' => 48.8566, 'lon' => 'east']],
            'not a number latitude' => [['lat' => NAN, 'lon' => 2.3522]],
            'infinite longitude' => [['lat' => 48.8566, 'lon' => INF]],
        ];
    }

    public function testIncompletePopulatedPlaceWrite(): void
    {
        $page = (new Page())->setProperties([
            'Location' => (new PlacePropertyValue())->setPlace(
                (new PlaceProperty())->setName('Missing coordinates'),
            ),
        ]);

        $this->expectException(InvalidPropertyValueException::class);

        $page->toArrayForUpdate();
    }

    private function getPageRawData(): array
    {
        return (array) json_decode(
            (string) file_get_contents('tests/Fixtures/client_pages_retrieve_page_200.json'),
            true,
        );
    }
}

<?php

declare(strict_types=1);

namespace Brd6\Test\NotionSdkPhp\Resource\Property\Value;

use Brd6\NotionSdkPhp\Resource\Page;
use Brd6\NotionSdkPhp\Resource\Page\PropertyValue\ButtonPropertyValue;
use Brd6\NotionSdkPhp\Resource\Page\PropertyValue\CheckboxPropertyValue;
use Brd6\NotionSdkPhp\Resource\Page\PropertyValue\CreatedByPropertyValue;
use Brd6\NotionSdkPhp\Resource\Page\PropertyValue\CreatedTimePropertyValue;
use Brd6\NotionSdkPhp\Resource\Page\PropertyValue\EmailPropertyValue;
use Brd6\NotionSdkPhp\Resource\Page\PropertyValue\FilesPropertyValue;
use Brd6\NotionSdkPhp\Resource\Page\PropertyValue\FormulaPropertyValue;
use Brd6\NotionSdkPhp\Resource\Page\PropertyValue\LastEditedByPropertyValue;
use Brd6\NotionSdkPhp\Resource\Page\PropertyValue\LastEditedTimePropertyValue;
use Brd6\NotionSdkPhp\Resource\Page\PropertyValue\PeoplePropertyValue;
use Brd6\NotionSdkPhp\Resource\Page\PropertyValue\PhoneNumberPropertyValue;
use Brd6\NotionSdkPhp\Resource\Page\PropertyValue\PlacePropertyValue;
use Brd6\NotionSdkPhp\Resource\Page\PropertyValue\RelationPropertyValue;
use Brd6\NotionSdkPhp\Resource\Page\PropertyValue\RichTextPropertyValue;
use Brd6\NotionSdkPhp\Resource\Page\PropertyValue\RollupPropertyValue;
use Brd6\NotionSdkPhp\Resource\Page\PropertyValue\StatusPropertyValue;
use Brd6\NotionSdkPhp\Resource\Page\PropertyValue\TitlePropertyValue;
use Brd6\NotionSdkPhp\Resource\Page\PropertyValue\UniqueIdPropertyValue;
use Brd6\NotionSdkPhp\Resource\Page\PropertyValue\UrlPropertyValue;
use Brd6\NotionSdkPhp\Resource\Page\PropertyValue\VerificationPropertyValue;
use Brd6\NotionSdkPhp\Resource\Property\AbstractProperty;
use Brd6\NotionSdkPhp\Resource\Property\Value\ArrayValueProperty;
use Brd6\NotionSdkPhp\Resource\Property\Value\BooleanValueProperty;
use Brd6\NotionSdkPhp\Resource\Property\Value\SelectValueProperty;
use Brd6\NotionSdkPhp\Resource\Property\Value\UnsupportedValueProperty;
use Brd6\Test\NotionSdkPhp\TestCase;

use function array_keys;
use function array_map;
use function count;
use function file_get_contents;
use function json_decode;

class ArrayValuePropertyTest extends TestCase
{
    public function testRollupArrayItemsHydrateAsTheirPropertyValueClass(): void
    {
        $expectedClasses = [
            'relation' => RelationPropertyValue::class,
            'title' => TitlePropertyValue::class,
            'rich_text' => RichTextPropertyValue::class,
            'people' => PeoplePropertyValue::class,
            'url' => UrlPropertyValue::class,
            'status' => StatusPropertyValue::class,
            'email' => EmailPropertyValue::class,
            'phone_number' => PhoneNumberPropertyValue::class,
            'checkbox' => CheckboxPropertyValue::class,
            'files' => FilesPropertyValue::class,
            'created_by' => CreatedByPropertyValue::class,
            'created_time' => CreatedTimePropertyValue::class,
            'last_edited_by' => LastEditedByPropertyValue::class,
            'last_edited_time' => LastEditedTimePropertyValue::class,
            'formula' => FormulaPropertyValue::class,
            'button' => ButtonPropertyValue::class,
            'unique_id' => UniqueIdPropertyValue::class,
            'verification' => VerificationPropertyValue::class,
            'place' => PlacePropertyValue::class,
            'select' => SelectValueProperty::class,
            'future_type' => UnsupportedValueProperty::class,
        ];

        $items = $this->hydrateRollupArray();
        $this->assertCount(count($expectedClasses), $items);

        foreach ($items as $index => $item) {
            $type = array_keys($expectedClasses)[$index];
            $this->assertSame($type, $item->getType());
            $this->assertInstanceOf($expectedClasses[$type], $item);
        }
    }

    public function testRollupArrayItemsExposeTheirParsedValues(): void
    {
        $items = $this->hydrateRollupArray();

        /** @var RelationPropertyValue $relation */
        $relation = $items[0];
        $this->assertSame(['5e3d4c2b-1a09-4f8e-9d7c-6b5a4f3e2d1c'], array_map(
            fn ($relationItem) => $relationItem->getId(),
            $relation->getRelation(),
        ));

        /** @var TitlePropertyValue $title */
        $title = $items[1];
        $this->assertSame('Linked article', $title->getTitle()[0]->getPlainText());

        /** @var StatusPropertyValue $status */
        $status = $items[5];
        $this->assertSame('In progress', $status->getStatus()->getName());

        /** @var FormulaPropertyValue $formula */
        $formula = $items[14];
        $this->assertInstanceOf(BooleanValueProperty::class, $formula->getFormula());

        /** @var UniqueIdPropertyValue $uniqueId */
        $uniqueId = $items[16];
        $this->assertSame('RL', $uniqueId->getPrefix());
        $this->assertSame(3, $uniqueId->getNumber());

        /** @var UnsupportedValueProperty $futureType */
        $futureType = $items[20];
        $this->assertSame(
            ['type' => 'future_type', 'future_type' => ['value' => 'kept as raw data']],
            $futureType->getRawData(),
        );
    }

    /**
     * @return array<int, AbstractProperty>
     */
    private function hydrateRollupArray(): array
    {
        /** @var Page $page */
        $page = Page::fromRawData($this->loadFixture());

        /** @var RollupPropertyValue $rollup */
        $rollup = $page->getProperties()['Rollup'];

        /** @var ArrayValueProperty $rollupValue */
        $rollupValue = $rollup->getRollup();
        $this->assertInstanceOf(ArrayValueProperty::class, $rollupValue);

        return $rollupValue->getArray();
    }

    /**
     * @return array<string, mixed>
     */
    private function loadFixture(): array
    {
        return (array) json_decode(
            (string) file_get_contents('tests/Fixtures/rollup_with_property_values_200.json'),
            true,
        );
    }
}

<?php

declare(strict_types=1);

namespace Brd6\Test\NotionSdkPhp\Resource;

use Brd6\NotionSdkPhp\Exception\InvalidResourceException;
use Brd6\NotionSdkPhp\Resource\DataSource;
use Brd6\NotionSdkPhp\Resource\Database\PropertyObject\RelationPropertyObject;
use Brd6\NotionSdkPhp\Resource\File\Icon;
use Brd6\NotionSdkPhp\Resource\Page\Parent\DatabaseIdParent;
use Brd6\NotionSdkPhp\Resource\Page\Parent\PageIdParent;
use PHPUnit\Framework\TestCase;

use function count;
use function file_get_contents;
use function json_decode;

class DataSourceTest extends TestCase
{
    public function testInvalidDataSource(): void
    {
        $this->expectException(InvalidResourceException::class);

        DataSource::fromRawData([]);
    }

    public function testDataSource(): void
    {
        /** @var DataSource $dataSource */
        $dataSource = DataSource::fromRawData(
            (array) json_decode(
                (string) file_get_contents('tests/Fixtures/client_data_sources_retrieve_200.json'),
                true,
            ),
        );

        $this->assertNotEmpty($dataSource->getId());
        $this->assertEquals(DataSource::RESOURCE_TYPE, $dataSource->getObject());
        $this->assertInstanceOf(DatabaseIdParent::class, $dataSource->getParent());
        $this->assertInstanceOf(PageIdParent::class, $dataSource->getDatabaseParent());
        $this->assertEquals('database_id', $dataSource->getParent()->getType());
        $this->assertEquals('page_id', $dataSource->getDatabaseParent()->getType());
        $this->assertFalse($dataSource->isInTrash());
        $this->assertFalse($dataSource->isArchived());
        $this->assertGreaterThan(0, count($dataSource->getProperties()));

        /** @var RelationPropertyObject $projectsProperty */
        $projectsProperty = $dataSource->getProperties()['Projects'];
        $this->assertSame(
            'a42a62ed-9b51-4b98-9dea-ea6d091bc508',
            $projectsProperty->getRelation()->getDataSourceId(),
        );
        $this->assertNotEmpty($dataSource->toArray());
    }

    public function testDataSourceWithIconObject(): void
    {
        /** @var array $rawData */
        $rawData = (array) json_decode(
            (string) file_get_contents('tests/Fixtures/client_data_sources_retrieve_200.json'),
            true,
        );

        $rawData['icon'] = [
            'type' => 'icon',
            'icon' => [
                'name' => 'book',
                'color' => 'gray',
            ],
        ];

        /** @var DataSource $dataSource */
        $dataSource = DataSource::fromRawData($rawData);

        $icon = $dataSource->getIcon();
        $this->assertNotNull($icon);
        $this->assertInstanceOf(Icon::class, $icon);
        $this->assertSame('icon', $icon->getType());
        $this->assertNotNull($icon->getIcon());
        $this->assertSame('book', $icon->getIcon()->getName());
        $this->assertSame('gray', $icon->getIcon()->getColor());
    }
}

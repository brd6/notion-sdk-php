<?php

declare(strict_types=1);

namespace Brd6\Test\NotionSdkPhp\Resource;

use Brd6\NotionSdkPhp\Exception\InvalidResourceException;
use Brd6\NotionSdkPhp\Resource\Database;
use Brd6\NotionSdkPhp\Resource\File\Emoji;
use Brd6\NotionSdkPhp\Resource\File\External;
use PHPUnit\Framework\TestCase;

use function count;
use function file_get_contents;
use function json_decode;

class DatabaseTest extends TestCase
{
    public function testInvalidDatabase(): void
    {
        $this->expectException(InvalidResourceException::class);

        Database::fromRawData([]);
    }

    public function testDatabase(): void
    {
        /** @var Database $database */
        $database = Database::fromRawData(
            (array) json_decode(
                (string) file_get_contents('tests/fixtures/client_databases_retrieve_database_200.json'),
                true,
            ),
        );

        $this->assertNotEmpty($database->getId());
        $this->assertNotEmpty($database->toArray());

        $icon = $database->getIcon();

        $this->assertNotEmpty($icon);
        $this->assertInstanceOf(Emoji::class, $icon);
        $this->assertEquals('emoji', $icon->getType());
        $this->assertNotEmpty($icon->getEmoji());

        $cover = $database->getCover();

        $this->assertNotEmpty($cover);
        $this->assertInstanceOf(External::class, $cover);
        $this->assertEquals('external', $cover->getType());

        $external = $cover->getExternal();

        $this->assertNotNull($external);
        $this->assertNotEmpty($external->getUrl());
    }

    public function testDatabaseProperties(): void
    {
        /** @var Database $database */
        $database = Database::fromRawData(
            (array) json_decode(
                (string) file_get_contents('tests/fixtures/client_databases_retrieve_database_200.json'),
                true,
            ),
        );

        $properties = $database->getProperties();

        $this->assertGreaterThan(0, count($database->getProperties()));

        foreach ($properties as $property) {
            $this->assertNotEmpty($property->getType());
            $this->assertNotEmpty($property->toArray());
        }
    }
}

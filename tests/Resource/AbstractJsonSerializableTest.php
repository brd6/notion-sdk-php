<?php

declare(strict_types=1);

namespace Brd6\Test\NotionSdkPhp\Resource;

use Brd6\NotionSdkPhp\Resource\AbstractJsonSerializable;
use PHPUnit\Framework\TestCase;

use function array_keys;
use function array_map;
use function json_encode;
use function range;

class AbstractJsonSerializableTest extends TestCase
{
    public function testToArrayStrictReturnsOnlyRequestedKeys(): void
    {
        $resource = $this->createResource('set');

        $this->assertSame(['foo' => 'foo'], $resource->toArrayStrict(['foo']));
    }

    public function testToArrayStrictRespectsIgnoreEmptyValueFlag(): void
    {
        $resource = $this->createResource(null);

        $this->assertSame([], $resource->toArrayStrict(['optional']));
        $this->assertSame(['optional' => null], $resource->toArrayStrict(['optional'], false));
    }

    public function testToArrayKeepsListsOfMoreThanTenItemsAsLists(): void
    {
        $resource = new class extends AbstractJsonSerializable {
            protected array $richText;

            public function __construct()
            {
                $this->richText = array_map(
                    fn (int $index) => ['plainText' => "segment $index"],
                    range(0, 10),
                );
            }
        };

        $richText = (array) $resource->toArray()['rich_text'];

        $this->assertSame(range(0, 10), array_keys($richText));
        $this->assertSame(['plain_text' => 'segment 10'], $richText[10]);
        $this->assertStringStartsWith('[', (string) json_encode($richText));
    }

    private function createResource(?string $optional): AbstractJsonSerializable
    {
        return new class ($optional) extends AbstractJsonSerializable {
            protected string $foo = 'foo';
            protected string $bar = 'bar';
            protected ?string $optional;

            public function __construct(?string $optional)
            {
                $this->optional = $optional;
            }
        };
    }
}

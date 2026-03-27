<?php

declare(strict_types=1);

namespace Brd6\Test\NotionSdkPhp\Resource;

use Brd6\NotionSdkPhp\Resource\AbstractJsonSerializable;
use PHPUnit\Framework\TestCase;

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

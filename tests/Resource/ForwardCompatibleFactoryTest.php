<?php

declare(strict_types=1);

namespace Brd6\Test\NotionSdkPhp\Resource;

use Brd6\NotionSdkPhp\Resource\Block\ForwardCompatibleBlockFactory;
use Brd6\NotionSdkPhp\Resource\Page\PropertyItem\ForwardCompatiblePropertyItemFactory;
use Brd6\NotionSdkPhp\Resource\Page\PropertyValue\ForwardCompatiblePropertyValueFactory;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class ForwardCompatibleFactoryTest extends TestCase
{
    /**
     * @dataProvider factoryClassProvider
     */
    public function testFactoryCannotBeInstantiated(string $class): void
    {
        $this->assertFalse((new ReflectionClass($class))->isInstantiable());
    }

    public function factoryClassProvider(): array
    {
        return [
            [ForwardCompatibleBlockFactory::class],
            [ForwardCompatiblePropertyItemFactory::class],
            [ForwardCompatiblePropertyValueFactory::class],
        ];
    }
}

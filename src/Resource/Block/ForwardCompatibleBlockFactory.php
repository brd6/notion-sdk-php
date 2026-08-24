<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Block;

use Brd6\NotionSdkPhp\Exception\InvalidResourceException;
use Brd6\NotionSdkPhp\Exception\InvalidResourceTypeException;
use Brd6\NotionSdkPhp\Exception\UnsupportedNotionExceptionInterface;
use Brd6\NotionSdkPhp\Resource\Block\Fallback\ReadOnlyUnsupportedBlock;
use Brd6\NotionSdkPhp\Resource\Property\AbstractParagraphProperty;
use Brd6\NotionSdkPhp\Resource\Property\SyncedBlockProperty;
use Brd6\NotionSdkPhp\Resource\Property\TableProperty;
use Brd6\NotionSdkPhp\Resource\User\AbstractUser;
use Brd6\NotionSdkPhp\Util\StringHelper;
use DateTimeImmutable;

use function array_keys;
use function array_map;
use function method_exists;

abstract class ForwardCompatibleBlockFactory extends AbstractBlock
{
    public static function create(array $rawData): AbstractBlock
    {
        if (!isset($rawData['object'], $rawData['type'])) {
            throw new InvalidResourceException();
        }

        if ($rawData['object'] !== self::getResourceType()) {
            throw new InvalidResourceTypeException((string) $rawData['object']);
        }

        $class = self::getMapClassFromType((string) $rawData['type']);

        if ($class === UnsupportedBlock::class) {
            return self::createUnsupportedBlock($rawData);
        }

        /** @var AbstractBlock $block */
        $block = new $class();
        $block->setRawData($rawData);

        try {
            self::initializeSupportedBlock($block);

            return $block;
        } catch (UnsupportedNotionExceptionInterface $exception) {
            return self::createUnsupportedBlock($rawData);
        }
    }

    private static function initializeSupportedBlock(AbstractBlock $block): void
    {
        $rawData = $block->getRawData();
        $block->type = (string) $rawData['type'];
        $block->createdTime = new DateTimeImmutable((string) $rawData['created_time']);
        $block->createdBy = AbstractUser::fromRawData((array) $rawData['created_by']);
        $block->lastEditedTime = new DateTimeImmutable((string) $rawData['last_edited_time']);
        $block->lastEditedBy = AbstractUser::fromRawData((array) $rawData['last_edited_by']);
        $block->archived = (bool) ($rawData['archived'] ?? $rawData['in_trash'] ?? false);
        $block->hasChildren = (bool) ($rawData['has_children'] ?? false);
        $inlineChildrenRawData = self::getInlineChildrenRawData($block);

        if ($inlineChildrenRawData !== null) {
            $block->setRawData(self::withoutInlineChildren($rawData, $block->getType()));
        }

        $block->initializeBlockProperty();
        $block->setRawData($rawData);
        self::initializeInlineChildren($block, $inlineChildrenRawData);
    }

    private static function createUnsupportedBlock(array $rawData): ReadOnlyUnsupportedBlock
    {
        $block = new ReadOnlyUnsupportedBlock();
        $block->setRawData($rawData);
        $block->type = (string) $rawData['type'];
        $block->archived = (bool) ($rawData['archived'] ?? $rawData['in_trash'] ?? false);
        $block->hasChildren = (bool) ($rawData['has_children'] ?? false);
        $block->createdTime = isset($rawData['created_time']) ?
            new DateTimeImmutable((string) $rawData['created_time']) :
            null;
        $block->lastEditedTime = isset($rawData['last_edited_time']) ?
            new DateTimeImmutable((string) $rawData['last_edited_time']) :
            null;
        $block->createdBy = isset($rawData['created_by']) ?
            self::createUser((array) $rawData['created_by']) :
            null;
        $block->lastEditedBy = isset($rawData['last_edited_by']) ?
            self::createUser((array) $rawData['last_edited_by']) :
            null;
        $block->initializeBlockProperty();
        self::initializeInlineChildren($block, self::getInlineChildrenRawData($block));

        return $block;
    }

    private static function createUser(array $rawData): ?AbstractUser
    {
        try {
            return AbstractUser::fromRawData($rawData);
        } catch (UnsupportedNotionExceptionInterface $exception) {
            return null;
        }
    }

    private static function getInlineChildrenRawData(AbstractBlock $block): ?array
    {
        if (!$block->hasChildren) {
            return null;
        }

        $rawData = $block->getRawData();
        $blockData = (array) ($rawData[$block->getType()] ?? []);

        if (!isset($blockData['children'])) {
            return null;
        }

        $children = (array) $blockData['children'];

        if (!self::hasSequentialKeys($children)) {
            return null;
        }

        return $children;
    }

    private static function hasSequentialKeys(array $children): bool
    {
        $position = 0;
        foreach (array_keys($children) as $key) {
            if ($key !== $position) {
                return false;
            }

            ++$position;
        }

        return true;
    }

    private static function withoutInlineChildren(array $rawData, string $type): array
    {
        $blockData = (array) ($rawData[$type] ?? []);
        unset($blockData['children']);
        $rawData[$type] = $blockData;

        return $rawData;
    }

    private static function initializeInlineChildren(AbstractBlock $block, ?array $rawData): void
    {
        if ($rawData === null) {
            return;
        }

        $children = array_map(fn (array $child) => self::create($child), $rawData);
        $block->children = $children;
        self::setPropertyChildren($block, $children);
    }

    /**
     * @param AbstractBlock[] $children
     */
    private static function setPropertyChildren(AbstractBlock $block, array $children): void
    {
        $typeFormatted = StringHelper::snakeCaseToCamelCase($block->getType());
        $getterMethodName = "get$typeFormatted";

        if (!method_exists($block, $getterMethodName)) {
            return;
        }

        $property = $block->getProperty();

        if (
            !$property instanceof AbstractParagraphProperty &&
            !$property instanceof TableProperty &&
            !$property instanceof SyncedBlockProperty
        ) {
            return;
        }

        $property->setChildren($children);
    }
}

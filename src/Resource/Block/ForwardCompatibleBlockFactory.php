<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Block;

use Brd6\NotionSdkPhp\Exception\InvalidResourceException;
use Brd6\NotionSdkPhp\Exception\InvalidResourceTypeException;
use Brd6\NotionSdkPhp\Exception\UnsupportedNotionExceptionInterface;
use Brd6\NotionSdkPhp\Resource\User\AbstractUser;
use DateTimeImmutable;

use function array_map;
use function is_array;

final class ForwardCompatibleBlockFactory extends AbstractBlock
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
        $block->initializeBlockProperty();
        $block->children = self::createChildren($block);
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
        $block->children = self::createChildren($block);

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

    /**
     * @return AbstractBlock[]
     */
    private static function createChildren(AbstractBlock $block): array
    {
        if (!$block->hasChildren) {
            return [];
        }

        $rawData = $block->getRawData();
        $blockData = (array) ($rawData[$block->getType()] ?? []);

        if (!isset($blockData['children'])) {
            return [];
        }

        $children = (array) $blockData['children'];

        if (!self::containsOnlyInlineChildren($children)) {
            return [];
        }

        return array_map(fn (array $child) => self::create($child), $children);
    }

    private static function containsOnlyInlineChildren(array $children): bool
    {
        $position = 0;
        foreach ($children as $key => $child) {
            if ($key !== $position || !is_array($child)) {
                return false;
            }

            ++$position;
        }

        return true;
    }

    protected function initializeBlockProperty(): void
    {
    }
}

<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource;

use Brd6\NotionSdkPhp\Exception\UnsupportedNotionExceptionInterface;
use Brd6\NotionSdkPhp\Resource\File\AbstractFile;
use Brd6\NotionSdkPhp\Resource\Page\Parent\AbstractParentProperty;
use Brd6\NotionSdkPhp\Resource\Page\PropertyValue\ForwardCompatiblePropertyValueFactory;
use Brd6\NotionSdkPhp\Resource\User\AbstractUser;
use DateTimeImmutable;

use function array_key_exists;

final class ForwardCompatiblePage extends Page
{
    protected function initialize(): void
    {
        $rawData = $this->getRawData();
        $this->createdBy = $this->hydrateSupportedContent(
            fn () => AbstractUser::fromRawData((array) $rawData['created_by']),
        );
        $this->createdTime = new DateTimeImmutable((string) $rawData['created_time']);
        $this->lastEditedTime = new DateTimeImmutable((string) $rawData['last_edited_time']);
        $this->lastEditedBy = $this->hydrateSupportedContent(
            fn () => AbstractUser::fromRawData((array) $rawData['last_edited_by']),
        );
        $this->archived = array_key_exists('archived', $rawData)
            ? (bool) $rawData['archived']
            : (array_key_exists('in_trash', $rawData) ? (bool) $rawData['in_trash'] : null);
        $this->isLocked = array_key_exists('is_locked', $rawData) ? (bool) $rawData['is_locked'] : null;
        $this->icon = isset($rawData['icon']) ? $this->hydrateSupportedContent(
            fn () => AbstractFile::fromRawData((array) $rawData['icon']),
        ) : null;
        $this->cover = isset($rawData['cover']) ? $this->hydrateSupportedContent(
            fn () => AbstractFile::fromRawData((array) $rawData['cover']),
        ) : null;
        $this->parent = $this->hydrateSupportedContent(
            fn () => AbstractParentProperty::fromRawData((array) $rawData['parent']),
        );
        $this->url = (string) $rawData['url'];

        /** @var array<string, array> $properties */
        $properties = (array) $rawData['properties'];
        foreach ($properties as $key => $property) {
            $this->properties[$key] = ForwardCompatiblePropertyValueFactory::create($property);
        }
    }

    /**
     * @param callable(): T $hydrate
     *
     * @return T|null
     *
     * @template T
     */
    private function hydrateSupportedContent(callable $hydrate)
    {
        try {
            return $hydrate();
        } catch (UnsupportedNotionExceptionInterface $exception) {
            return null;
        }
    }
}

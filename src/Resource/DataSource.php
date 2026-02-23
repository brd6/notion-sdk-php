<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource;

use Brd6\NotionSdkPhp\Exception\InvalidFileException;
use Brd6\NotionSdkPhp\Exception\InvalidParentException;
use Brd6\NotionSdkPhp\Exception\InvalidPropertyObjectException;
use Brd6\NotionSdkPhp\Exception\UnsupportedFileTypeException;
use Brd6\NotionSdkPhp\Exception\UnsupportedParentTypeException;
use Brd6\NotionSdkPhp\Exception\UnsupportedPropertyObjectException;
use Brd6\NotionSdkPhp\Exception\UnsupportedUserTypeException;
use Brd6\NotionSdkPhp\Resource\Database\PropertyObject\AbstractPropertyObject;
use Brd6\NotionSdkPhp\Resource\File\AbstractFile;
use Brd6\NotionSdkPhp\Resource\Page\Parent\AbstractParentProperty;
use Brd6\NotionSdkPhp\Resource\RichText\AbstractRichText;
use Brd6\NotionSdkPhp\Resource\User\AbstractUser;
use DateTimeImmutable;

use function array_map;

class DataSource extends AbstractResource
{
    public const RESOURCE_TYPE = 'data_source';
    private const UPDATE_ACCEPTED_KEYS = ['title', 'properties', 'in_trash'];

    protected ?DateTimeImmutable $createdTime = null;
    protected ?UserInterface $createdBy = null;
    protected ?DateTimeImmutable $lastEditedTime = null;
    protected ?UserInterface $lastEditedBy = null;

    /**
     * @var array|AbstractRichText[]
     */
    protected array $title = [];

    /**
     * @var array|AbstractRichText[]
     */
    protected array $description = [];

    protected ?AbstractFile $icon = null;

    /**
     * @var array<string, AbstractPropertyObject>
     */
    protected array $properties = [];

    protected ?AbstractParentProperty $parent = null;
    protected ?AbstractParentProperty $databaseParent = null;
    protected bool $archived = false;
    protected bool $inTrash = false;

    public function __construct()
    {
        parent::__construct();

        $this->object = self::RESOURCE_TYPE;
    }

    public function toArrayForUpdate(): array
    {
        return $this->toArray(true, self::UPDATE_ACCEPTED_KEYS);
    }

    /**
     * @throws InvalidFileException
     * @throws InvalidParentException
     * @throws InvalidPropertyObjectException
     * @throws UnsupportedFileTypeException
     * @throws UnsupportedParentTypeException
     * @throws UnsupportedPropertyObjectException
     * @throws UnsupportedUserTypeException
     */
    protected function initialize(): void
    {
        $this->createdBy = AbstractUser::fromRawData((array) ($this->getRawData()['created_by'] ?? []));
        $this->createdTime = isset($this->getRawData()['created_time']) ?
            new DateTimeImmutable((string) $this->getRawData()['created_time']) :
            null;
        $this->lastEditedTime = isset($this->getRawData()['last_edited_time']) ?
            new DateTimeImmutable((string) $this->getRawData()['last_edited_time']) :
            null;
        $this->lastEditedBy = AbstractUser::fromRawData((array) ($this->getRawData()['last_edited_by'] ?? []));
        $this->archived = (bool) ($this->getRawData()['archived'] ?? false);
        $this->inTrash = (bool) ($this->getRawData()['in_trash'] ?? false);
        $this->icon = isset($this->getRawData()['icon']) ?
            AbstractFile::fromRawData((array) $this->getRawData()['icon']) :
            null;
        $this->parent = isset($this->getRawData()['parent']) ?
            AbstractParentProperty::fromRawData((array) $this->getRawData()['parent']) :
            null;
        $this->databaseParent = isset($this->getRawData()['database_parent']) ?
            AbstractParentProperty::fromRawData((array) $this->getRawData()['database_parent']) :
            null;
        $this->title = isset($this->getRawData()['title']) ? array_map(
            fn (array $richTextRawData) => AbstractRichText::fromRawData($richTextRawData),
            (array) $this->getRawData()['title'],
        ) : [];
        $this->description = isset($this->getRawData()['description']) ? array_map(
            fn (array $richTextRawData) => AbstractRichText::fromRawData($richTextRawData),
            (array) $this->getRawData()['description'],
        ) : [];

        /** @var array<string, array> $properties */
        $properties = (array) ($this->getRawData()['properties'] ?? []);
        foreach ($properties as $key => $property) {
            $this->properties[$key] = AbstractPropertyObject::fromRawData($property);
        }
    }

    public static function getResourceType(): string
    {
        return self::RESOURCE_TYPE;
    }

    public function getCreatedTime(): ?DateTimeImmutable
    {
        return $this->createdTime;
    }

    public function setCreatedTime(?DateTimeImmutable $createdTime): self
    {
        $this->createdTime = $createdTime;

        return $this;
    }

    public function getCreatedBy(): ?UserInterface
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?UserInterface $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getLastEditedTime(): ?DateTimeImmutable
    {
        return $this->lastEditedTime;
    }

    public function setLastEditedTime(?DateTimeImmutable $lastEditedTime): self
    {
        $this->lastEditedTime = $lastEditedTime;

        return $this;
    }

    public function getLastEditedBy(): ?UserInterface
    {
        return $this->lastEditedBy;
    }

    public function setLastEditedBy(?UserInterface $lastEditedBy): self
    {
        $this->lastEditedBy = $lastEditedBy;

        return $this;
    }

    public function getTitle(): array
    {
        return $this->title;
    }

    public function setTitle(array $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): array
    {
        return $this->description;
    }

    public function setDescription(array $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getIcon(): ?AbstractFile
    {
        return $this->icon;
    }

    public function setIcon(?AbstractFile $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * @return array<string, AbstractPropertyObject>
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * @param array<string, AbstractPropertyObject> $properties
     */
    public function setProperties(array $properties): self
    {
        $this->properties = $properties;

        return $this;
    }

    public function getParent(): ?AbstractParentProperty
    {
        return $this->parent;
    }

    public function setParent(?AbstractParentProperty $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    public function getDatabaseParent(): ?AbstractParentProperty
    {
        return $this->databaseParent;
    }

    public function setDatabaseParent(?AbstractParentProperty $databaseParent): self
    {
        $this->databaseParent = $databaseParent;

        return $this;
    }

    public function isArchived(): bool
    {
        return $this->archived;
    }

    public function setArchived(bool $archived): self
    {
        $this->archived = $archived;

        return $this;
    }

    public function isInTrash(): bool
    {
        return $this->inTrash;
    }

    public function setInTrash(bool $inTrash): self
    {
        $this->inTrash = $inTrash;

        return $this;
    }
}

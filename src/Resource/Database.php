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
use Brd6\NotionSdkPhp\Resource\Database\PartialDataSource;
use Brd6\NotionSdkPhp\Resource\Database\PropertyObject\AbstractPropertyObject;
use Brd6\NotionSdkPhp\Resource\File\AbstractFile;
use Brd6\NotionSdkPhp\Resource\Page\Parent\AbstractParentProperty;
use Brd6\NotionSdkPhp\Resource\RichText\AbstractRichText;
use Brd6\NotionSdkPhp\Resource\User\AbstractUser;
use DateTimeImmutable;

use function array_key_exists;
use function array_map;

class Database extends AbstractResource
{
    public const RESOURCE_TYPE = 'database';
    private const UPDATE_ACCEPTED_KEYS = ['title', 'properties', 'icon', 'cover', 'is_locked'];

    protected ?DateTimeImmutable $createdTime = null;
    protected ?UserInterface $createdBy = null;
    protected ?DateTimeImmutable $lastEditedTime = null;
    protected ?UserInterface $lastEditedBy = null;

    /**
     * @var array|AbstractRichText[]
     */
    protected array $title = [];

    protected ?AbstractFile $icon = null;
    protected ?AbstractFile $cover = null;

    /**
     * @var array<string, AbstractPropertyObject>
     */
    protected array $properties = [];

    /**
     * @var PartialDataSource[]
     */
    protected array $dataSources = [];

    protected ?AbstractParentProperty $parent = null;
    protected string $url = '';
    protected bool $archived = false;
    protected ?bool $isLocked = null;

    public function __construct()
    {
        parent::__construct();

        $this->object = self::RESOURCE_TYPE;
    }

    public function toArrayForUpdate(): array
    {
        $data = $this->toArray(true, self::UPDATE_ACCEPTED_KEYS);

        if ($this->isLocked === null) {
            unset($data['is_locked']);
        }

        return $data;
    }

    /**
     * @throws InvalidFileException
     * @throws InvalidParentException
     * @throws InvalidPropertyObjectException
     * @throws UnsupportedFileTypeException
     * @throws UnsupportedParentTypeException
     * @throws UnsupportedUserTypeException
     * @throws UnsupportedPropertyObjectException
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
        $this->archived = (bool) ($this->getRawData()['archived'] ?? $this->getRawData()['in_trash'] ?? false);
        $this->isLocked = array_key_exists('is_locked', $this->getRawData())
            ? (bool) $this->getRawData()['is_locked']
            : null;
        $this->icon = isset($this->getRawData()['icon']) ?
            AbstractFile::fromRawData((array) $this->getRawData()['icon']) :
            null;
        $this->cover = isset($this->getRawData()['cover']) ?
            AbstractFile::fromRawData((array) $this->getRawData()['cover']) :
            null;
        $this->parent = AbstractParentProperty::fromRawData((array) $this->getRawData()['parent']);
        $this->url = (string) $this->getRawData()['url'];
        $this->title = isset($this->getRawData()['title']) ? array_map(
            fn (array $richTextRawData) => AbstractRichText::fromRawData($richTextRawData),
            (array) $this->getRawData()['title'],
        ) : [];
        $this->dataSources = isset($this->getRawData()['data_sources']) ? array_map(
            fn (array $item) => PartialDataSource::fromRawData($item),
            (array) $this->getRawData()['data_sources'],
        ) : [];

        /** @var array<string, array> $properties */
        $properties = (array) ($this->getRawData()['properties'] ?? []);
        foreach ($properties as $key => $property) {
            $this->properties[$key] = AbstractPropertyObject::fromRawData($property);
        }
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
        return $this->isArchived();
    }

    public function isLocked(): bool
    {
        return $this->isLocked ?? false;
    }

    public function setLocked(bool $isLocked): self
    {
        $this->isLocked = $isLocked;

        return $this;
    }

    public function setInTrash(bool $inTrash): self
    {
        return $this->setArchived($inTrash);
    }

    public static function getResourceType(): string
    {
        return self::RESOURCE_TYPE;
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

    public function getCover(): ?AbstractFile
    {
        return $this->cover;
    }

    public function setCover(?AbstractFile $cover): self
    {
        $this->cover = $cover;

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

    /**
     * @return PartialDataSource[]
     */
    public function getDataSources(): array
    {
        return $this->dataSources;
    }

    /**
     * @param PartialDataSource[] $dataSources
     */
    public function setDataSources(array $dataSources): self
    {
        $this->dataSources = $dataSources;

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

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return array|AbstractRichText[]
     */
    public function getTitle(): array
    {
        return $this->title;
    }

    /**
     * @param array|AbstractRichText[] $title
     */
    public function setTitle(array $title): self
    {
        $this->title = $title;

        return $this;
    }
}

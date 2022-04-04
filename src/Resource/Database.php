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

class Database extends AbstractResource
{
    public const RESOURCE_TYPE = 'database';

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

    protected ?AbstractParentProperty $parent = null;
    protected string $url = '';
    protected bool $archived = false;

    public function __construct()
    {
        parent::__construct();

        $this->object = self::RESOURCE_TYPE;
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
        $this->createdBy = AbstractUser::fromRawData((array) $this->getRawData()['created_by']);
        $this->createdTime = new DateTimeImmutable((string) $this->getRawData()['created_time']);
        $this->lastEditedTime = new DateTimeImmutable((string) $this->getRawData()['last_edited_time']);
        $this->lastEditedBy = AbstractUser::fromRawData((array) $this->getRawData()['last_edited_by']);
        $this->archived = (bool) $this->getRawData()['archived'];
        $this->icon = isset($this->getRawData()['icon']) ?
            AbstractFile::fromRawData((array) $this->getRawData()['icon']) :
            null;
        $this->cover = isset($this->getRawData()['cover']) ?
            AbstractFile::fromRawData((array) $this->getRawData()['cover']) :
            null;
        $this->parent = AbstractParentProperty::fromRawData((array) $this->getRawData()['parent']);
        $this->url = (string) $this->getRawData()['url'];

        /** @var array<string, array> $properties */
        $properties = (array) $this->getRawData()['properties'];
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

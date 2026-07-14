<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource;

use Brd6\NotionSdkPhp\Exception\InvalidFileException;
use Brd6\NotionSdkPhp\Exception\InvalidParentException;
use Brd6\NotionSdkPhp\Exception\InvalidPropertyValueException;
use Brd6\NotionSdkPhp\Exception\UnsupportedFileTypeException;
use Brd6\NotionSdkPhp\Exception\UnsupportedParentTypeException;
use Brd6\NotionSdkPhp\Exception\UnsupportedPropertyValueException;
use Brd6\NotionSdkPhp\Exception\UnsupportedUserTypeException;
use Brd6\NotionSdkPhp\Resource\File\AbstractFile;
use Brd6\NotionSdkPhp\Resource\Page\Parent\AbstractParentProperty;
use Brd6\NotionSdkPhp\Resource\Page\PropertyValue\AbstractPropertyValue;
use Brd6\NotionSdkPhp\Resource\User\AbstractUser;
use Brd6\NotionSdkPhp\Util\StringHelper;
use DateTimeImmutable;

use function array_diff_key;
use function array_key_exists;
use function in_array;
use function is_array;

class Page extends AbstractResource
{
    public const RESOURCE_TYPE = 'page';
    private const CREATE_ACCEPTED_KEYS = ['object', 'properties', 'parent', 'icon', 'cover'];
    private const UPDATE_ACCEPTED_KEYS = ['properties', 'archived', 'icon', 'cover', 'is_locked'];
    private const READ_ONLY_PROPERTY_VALUE_TYPES = [
        'created_by',
        'created_time',
        'last_edited_by',
        'last_edited_time',
        'formula',
        'rollup',
        'unique_id',
    ];

    protected ?DateTimeImmutable $createdTime = null;
    protected ?UserInterface $createdBy = null;
    protected ?DateTimeImmutable $lastEditedTime = null;
    protected ?UserInterface $lastEditedBy = null;
    protected ?bool $archived = null;
    protected ?bool $isLocked = null;
    protected ?AbstractFile $icon = null;
    protected ?AbstractFile $cover = null;

    /**
     * @var array<string, AbstractPropertyValue>
     */
    protected array $properties = [];

    protected ?AbstractParentProperty $parent = null;
    protected string $url = '';

    public function __construct()
    {
        parent::__construct();

        $this->object = self::RESOURCE_TYPE;
    }

    public function toArrayForCreate(): array
    {
        $data = $this->removeReadOnlyPropertyValues($this->toArrayStrict(self::CREATE_ACCEPTED_KEYS));

        if ($this->archived !== null) {
            $data['archived'] = $this->archived;
        }

        return $data;
    }

    public function toArrayForUpdate(): array
    {
        return $this->removeReadOnlyPropertyValues($this->toArrayStrict(self::UPDATE_ACCEPTED_KEYS));
    }

    /**
     * @psalm-suppress MixedAssignment
     */
    private function removeReadOnlyPropertyValues(array $data): array
    {
        if (!isset($data['properties']) || !is_array($data['properties'])) {
            return $data;
        }

        foreach ($this->properties as $name => $propertyValue) {
            $serializedName = StringHelper::camelCaseToSnakeCase($name);

            if (in_array($propertyValue->getType(), self::READ_ONLY_PROPERTY_VALUE_TYPES, true)) {
                unset($data['properties'][$serializedName]);

                continue;
            }

            $serialized = $data['properties'][$serializedName] ?? null;

            if (!is_array($serialized)) {
                continue;
            }

            $hasValue = false;
            foreach (array_diff_key($serialized, ['id' => true, 'type' => true]) as $value) {
                if ($value !== null && $value !== []) {
                    $hasValue = true;

                    break;
                }
            }

            if (!$hasValue) {
                unset($data['properties'][$serializedName]);
            }
        }

        return $data;
    }

    /**
     * @throws InvalidFileException
     * @throws InvalidParentException
     * @throws InvalidPropertyValueException
     * @throws UnsupportedFileTypeException
     * @throws UnsupportedParentTypeException
     * @throws UnsupportedUserTypeException
     * @throws UnsupportedPropertyValueException
     */
    protected function initialize(): void
    {
        $this->createdBy = AbstractUser::fromRawData((array) $this->getRawData()['created_by']);
        $this->createdTime = new DateTimeImmutable((string) $this->getRawData()['created_time']);
        $this->lastEditedTime = new DateTimeImmutable((string) $this->getRawData()['last_edited_time']);
        $this->lastEditedBy = AbstractUser::fromRawData((array) $this->getRawData()['last_edited_by']);
        $this->archived = array_key_exists('archived', $this->getRawData())
            ? (bool) $this->getRawData()['archived']
            : (array_key_exists('in_trash', $this->getRawData())
                ? (bool) $this->getRawData()['in_trash']
                : null);
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

        /** @var array<string, array> $properties */
        $properties = (array) $this->getRawData()['properties'];
        foreach ($properties as $key => $property) {
            $this->properties[$key] = AbstractPropertyValue::fromRawData($property);
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
        return $this->archived ?? false;
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
     * @return array<string, AbstractPropertyValue>
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * @param array<string, AbstractPropertyValue> $properties
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
}

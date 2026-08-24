<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Block;

use Brd6\NotionSdkPhp\Exception\InvalidResourceException;
use Brd6\NotionSdkPhp\Exception\InvalidResourceTypeException;
use Brd6\NotionSdkPhp\Exception\UnsupportedNotionExceptionInterface;
use Brd6\NotionSdkPhp\Exception\UnsupportedUserTypeException;
use Brd6\NotionSdkPhp\Resource\AbstractResource;
use Brd6\NotionSdkPhp\Resource\Property\AbstractProperty;
use Brd6\NotionSdkPhp\Resource\User\AbstractUser;
use Brd6\NotionSdkPhp\Resource\UserInterface;
use Brd6\NotionSdkPhp\Util\StringHelper;
use DateTimeImmutable;
use ReflectionClass;

use function array_map;
use function class_exists;
use function count;
use function is_array;
use function is_subclass_of;
use function preg_replace;

abstract class AbstractBlock extends AbstractResource
{
    public const RESOURCE_TYPE = 'block';

    protected string $type = '';
    protected ?DateTimeImmutable $createdTime = null;
    protected ?UserInterface $createdBy = null;
    protected ?DateTimeImmutable $lastEditedTime = null;
    protected ?UserInterface $lastEditedBy = null;
    protected bool $archived = false;
    protected bool $hasChildren = false;
    private bool $fallbackOnUnsupportedContent = false;

    /**
     * @var array|AbstractBlock[]
     */
    protected array $children = [];

    public function __construct()
    {
        parent::__construct();

        $this->object = self::RESOURCE_TYPE;
        $this->type = self::resolveType();
    }

    /**
     * @throws InvalidResourceException
     * @throws InvalidResourceTypeException
     * @throws UnsupportedUserTypeException
     */
    public static function fromRawData(array $rawData): self
    {
        return self::hydrateRawData($rawData, false);
    }

    public static function fromRawDataWithUnsupportedContentFallback(array $rawData): self
    {
        return self::hydrateRawData($rawData, true);
    }

    private static function hydrateRawData(array $rawData, bool $fallbackOnUnsupportedContent): self
    {
        if (
            !isset($rawData['object']) ||
            !isset($rawData['type'])
        ) {
            throw new InvalidResourceException();
        }

        if ($rawData['object'] !== static::getResourceType()) {
            throw new InvalidResourceTypeException((string) $rawData['object']);
        }

        $class = static::getMapClassFromType((string) $rawData['type']);

        /** @var self $resource */
        $resource = new $class();
        $resource->fallbackOnUnsupportedContent = $fallbackOnUnsupportedContent;

        try {
            $resource
                ->setRawData($rawData)
                ->initialize();
        } catch (UnsupportedNotionExceptionInterface $exception) {
            if (!$fallbackOnUnsupportedContent) {
                throw $exception;
            }

            $resource = new UnsupportedBlock();
            $resource
                ->setRawData($rawData)
                ->initialize();
        }

        if ($fallbackOnUnsupportedContent && $resource instanceof UnsupportedBlock) {
            self::initializeFallbackData($resource);
        }

        return $resource;
    }

    /**
     * @throws UnsupportedUserTypeException
     */
    protected function initialize(): void
    {
        $this->type = (string) $this->getRawData()['type'];
        $this->createdTime = new DateTimeImmutable((string) $this->getRawData()['created_time']);
        $this->createdBy = AbstractUser::fromRawData((array) $this->getRawData()['created_by']);
        $this->lastEditedTime = new DateTimeImmutable((string) $this->getRawData()['last_edited_time']);
        $this->lastEditedBy = AbstractUser::fromRawData((array) $this->getRawData()['last_edited_by']);
        $this->archived = (bool) ($this->getRawData()['archived'] ?? $this->getRawData()['in_trash'] ?? false);
        $this->hasChildren = (bool) ($this->getRawData()['has_children'] ?? false);

        if ($this->fallbackOnUnsupportedContent) {
            $this->initializeBlockProperty();
            $this->initializeChildren();

            return;
        }

        $this->initializeChildren();
        $this->initializeBlockProperty();
    }

    /**
     * @throws InvalidResourceException
     * @throws InvalidResourceTypeException
     * @throws UnsupportedUserTypeException
     */
    protected function initializeChildren(): void
    {
        if (!$this->hasChildren) {
            return;
        }

        $blockData = $this->getRawData()[$this->getType()] ?? [];

        if (!isset($blockData['children'])) {
            return;
        }

        $children = (array) $blockData['children'];

        if ($this->fallbackOnUnsupportedContent && !$this->hasOnlyInlineChildren($children)) {
            return;
        }

        $this->children = array_map(
            fn (array $childRawData) => $this->fallbackOnUnsupportedContent ?
                self::fromRawDataWithUnsupportedContentFallback($childRawData) :
                self::fromRawData($childRawData),
            $children,
        );
    }

    abstract protected function initializeBlockProperty(): void;

    protected static function getMapClassFromType(string $type): string
    {
        $typeFormatted = StringHelper::snakeCaseToCamelCase($type);
        $class = "Brd6\\NotionSdkPhp\\Resource\\Block\\{$typeFormatted}Block";

        if (!class_exists($class) || !is_subclass_of($class, self::class)) {
            return UnsupportedBlock::class;
        }

        return (new ReflectionClass($class))->isInstantiable() ? $class : UnsupportedBlock::class;
    }

    private static function resolveType(): string
    {
        return (string) preg_replace(
            '/(_?' . self::getResourceType() . ')$/i',
            '',
            StringHelper::camelCaseToSnakeCase(
                (new ReflectionClass(static::class))->getShortName(),
            ),
        );
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
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

    public function setInTrash(bool $inTrash): self
    {
        return $this->setArchived($inTrash);
    }

    public function isHasChildren(): bool
    {
        return $this->hasChildren;
    }

    public function setHasChildren(bool $hasChildren): self
    {
        $this->hasChildren = $hasChildren;

        return $this;
    }

    public static function getResourceType(): string
    {
        return self::RESOURCE_TYPE;
    }

    protected function getProperty(): ?AbstractProperty
    {
        $typeFormatted = StringHelper::snakeCaseToCamelCase($this->getType());
        $getterMethodName = "get$typeFormatted";

        /** @var AbstractProperty|null $property */
        $property = $this->$getterMethodName();

        return $property;
    }

    /**
     * @psalm-suppress PossiblyNullReference
     */
    public function propertyToArray(): array
    {
        return $this->getProperty() !== null ? $this->getProperty()->toArray() : [];
    }

    public function toArrayForCreate(): array
    {
        $data = $this->toArrayStrict(['object', 'type', $this->getType()]);

        if (count($this->children) > 0) {
            $property = (array) ($data[$this->getType()] ?? []);
            $property['children'] = array_map(
                fn (AbstractBlock $child) => $child->toArrayForCreate(),
                $this->children,
            );
            $data[$this->getType()] = $property;
        }

        return $data;
    }

    private function hasOnlyInlineChildren(array $children): bool
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

    private static function initializeFallbackData(self $resource): void
    {
        $resource->fallbackOnUnsupportedContent = true;
        $rawData = $resource->getRawData();
        $resource->archived = (bool) ($rawData['archived'] ?? $rawData['in_trash'] ?? false);
        $resource->hasChildren = (bool) ($rawData['has_children'] ?? false);
        $resource->createdTime = isset($rawData['created_time']) ?
            new DateTimeImmutable((string) $rawData['created_time']) :
            null;
        $resource->lastEditedTime = isset($rawData['last_edited_time']) ?
            new DateTimeImmutable((string) $rawData['last_edited_time']) :
            null;

        if (isset($rawData['created_by'])) {
            try {
                $resource->createdBy = AbstractUser::fromRawData((array) $rawData['created_by']);
            } catch (UnsupportedNotionExceptionInterface $exception) {
                $resource->createdBy = null;
            }
        }

        if (isset($rawData['last_edited_by'])) {
            try {
                $resource->lastEditedBy = AbstractUser::fromRawData((array) $rawData['last_edited_by']);
            } catch (UnsupportedNotionExceptionInterface $exception) {
                $resource->lastEditedBy = null;
            }
        }

        $resource->initializeChildren();
    }

    /**
     * @return array|AbstractBlock[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * @param array|AbstractBlock[] $children
     */
    public function setChildren(array $children): self
    {
        $this->children = $children;

        return $this;
    }
}

<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Block;

use Brd6\NotionSdkPhp\Exception\InvalidResourceException;
use Brd6\NotionSdkPhp\Exception\InvalidResourceTypeException;
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

        /** @var static $resource */
        $resource = new $class();

        $resource
            ->setRawData($rawData)
            ->initialize();

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
        $this->archived = (bool) $this->getRawData()['archived'];
        $this->hasChildren = (bool) $this->getRawData()['has_children'];

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

        $this->children = array_map(
            fn (array $childRawData) => self::fromRawData($childRawData),
            (array) $blockData['children'],
        );
    }

    abstract protected function initializeBlockProperty(): void;

    protected static function getMapClassFromType(string $type): string
    {
        $typeFormatted = StringHelper::snakeCaseToCamelCase($type);
        $class = "Brd6\\NotionSdkPhp\\Resource\\Block\\{$typeFormatted}Block";

        return class_exists($class) ? $class : UnsupportedBlock::class;
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

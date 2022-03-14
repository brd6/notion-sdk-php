<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource;

use Brd6\NotionSdkPhp\Exception\InvalidResourceException;
use Brd6\NotionSdkPhp\Exception\InvalidResourceTypeException;
use Brd6\NotionSdkPhp\Resource\Block\UnsupportedBlock;
use Brd6\NotionSdkPhp\Util\StringHelper;
use DateTimeImmutable;

use function class_exists;

abstract class Block extends AbstractResource
{
    public const RESOURCE_TYPE = 'block';

    protected string $type = '';
    protected ?DateTimeImmutable $createdTime = null;
    protected ?PartialUser $createdBy = null;
    protected ?DateTimeImmutable $lastEditedTime = null;
    protected ?PartialUser $lastEditedBy = null;
    protected bool $archived = false;
    protected bool $hasChildren = false;

    /**
     * @throws InvalidResourceTypeException
     * @throws InvalidResourceException
     */
    public static function fromResponseData(array $responseData): self
    {
        if (
            !isset($responseData['object']) ||
            !isset($responseData['type'])
        ) {
            throw new InvalidResourceException();
        }

        if ($responseData['object'] !== static::getResourceType()) {
            throw new InvalidResourceTypeException((string) $responseData['object']);
        }

        $class = static::getBlockMapClassFromType((string) $responseData['type']);

        /** @var static $resource */
        $resource = new $class();

        $resource
            ->setResponseData($responseData)
            ->initialize();

        return $resource;
    }

    /**
     * @throws InvalidResourceTypeException
     */
    protected function initialize(): void
    {
        $this->type = (string) $this->getResponseData()['type'];
        $this->createdTime = new DateTimeImmutable((string) $this->getResponseData()['created_time']);
        $this->createdBy = PartialUser::fromResponseData((array) $this->getResponseData()['created_by']);
        $this->lastEditedTime = new DateTimeImmutable((string) $this->getResponseData()['last_edited_time']);
        $this->lastEditedBy = PartialUser::fromResponseData((array) $this->getResponseData()['last_edited_by']);
        $this->archived = (bool) $this->getResponseData()['archived'];
        $this->hasChildren = (bool) $this->getResponseData()['has_children'];

        $this->initializeBlockProperty();
    }

    abstract protected function initializeBlockProperty(): void;

    protected static function getBlockMapClassFromType(string $type): string
    {
        $typeFormatted = StringHelper::snakeCaseToCamelCase($type);
        $class = "Brd6\\NotionSdkPhp\\Resource\Block\\${typeFormatted}Block";

        return class_exists($class) ? $class : UnsupportedBlock::class;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): Block
    {
        $this->type = $type;

        return $this;
    }

    public function getCreatedTime(): ?DateTimeImmutable
    {
        return $this->createdTime;
    }

    public function setCreatedTime(?DateTimeImmutable $createdTime): Block
    {
        $this->createdTime = $createdTime;

        return $this;
    }

    public function getCreatedBy(): ?PartialUser
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?PartialUser $createdBy): Block
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getLastEditedTime(): ?DateTimeImmutable
    {
        return $this->lastEditedTime;
    }

    public function setLastEditedTime(?DateTimeImmutable $lastEditedTime): Block
    {
        $this->lastEditedTime = $lastEditedTime;

        return $this;
    }

    public function getLastEditedBy(): ?PartialUser
    {
        return $this->lastEditedBy;
    }

    public function setLastEditedBy(?PartialUser $lastEditedBy): Block
    {
        $this->lastEditedBy = $lastEditedBy;

        return $this;
    }

    public function isArchived(): bool
    {
        return $this->archived;
    }

    public function setArchived(bool $archived): Block
    {
        $this->archived = $archived;

        return $this;
    }

    public function isHasChildren(): bool
    {
        return $this->hasChildren;
    }

    public function setHasChildren(bool $hasChildren): Block
    {
        $this->hasChildren = $hasChildren;

        return $this;
    }

    public static function getResourceType(): string
    {
        return self::RESOURCE_TYPE;
    }
}

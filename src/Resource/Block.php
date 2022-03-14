<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource;

use Brd6\NotionSdkPhp\Exception\InvalidResourceTypeException;
use Brd6\NotionSdkPhp\Resource\Block\UnsupportedBlock;
use Brd6\NotionSdkPhp\Util\StringHelper;
use DateTimeImmutable;

use function class_exists;

abstract class Block extends AbstractResource
{
    public const RESOURCE_TYPE = 'block';

    protected string $type;
    protected DateTimeImmutable $createdTime;
    protected PartialUser $createdBy;
    protected DateTimeImmutable $lastEditedTime;
    protected PartialUser $lastEditedBy;
    protected bool $archived;
    protected bool $hasChildren;

    /**
     * @throws InvalidResourceTypeException
     */
    public static function fromResponseData(array $responseData): self
    {
        if (
            !isset($responseData['object']) ||
            !isset($responseData['type']) ||
            $responseData['object'] !== static::getResourceType()
        ) {
            throw new InvalidResourceTypeException($responseData['object']);
        }

        $class = static::getBlockMapClassFromType($responseData['type']);

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
        $this->type = $this->getResponseData()['type'];
        $this->createdTime = new DateTimeImmutable($this->getResponseData()['created_time']);
        $this->createdBy = PartialUser::fromResponseData($this->getResponseData()['created_by']);
        $this->lastEditedTime = new DateTimeImmutable($this->getResponseData()['last_edited_time']);
        $this->lastEditedBy = PartialUser::fromResponseData($this->getResponseData()['last_edited_by']);
        $this->archived = $this->getResponseData()['archived'];
        $this->hasChildren = $this->getResponseData()['has_children'];

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

    public function getCreatedTime(): DateTimeImmutable
    {
        return $this->createdTime;
    }

    public function setCreatedTime(DateTimeImmutable $createdTime): Block
    {
        $this->createdTime = $createdTime;

        return $this;
    }

    public function getCreatedBy(): PartialUser
    {
        return $this->createdBy;
    }

    public function setCreatedBy(PartialUser $createdBy): Block
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getLastEditedTime(): DateTimeImmutable
    {
        return $this->lastEditedTime;
    }

    public function setLastEditedTime(DateTimeImmutable $lastEditedTime): Block
    {
        $this->lastEditedTime = $lastEditedTime;

        return $this;
    }

    public function getLastEditedBy(): PartialUser
    {
        return $this->lastEditedBy;
    }

    public function setLastEditedBy(PartialUser $lastEditedBy): Block
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
}

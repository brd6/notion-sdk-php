<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\User;

use Brd6\NotionSdkPhp\Exception\UnsupportedUserTypeException;
use Brd6\NotionSdkPhp\Resource\AbstractResource;
use Brd6\NotionSdkPhp\Resource\UserInterface;
use Brd6\NotionSdkPhp\Util\StringHelper;

use function class_exists;

abstract class AbstractUser extends AbstractResource implements UserInterface
{
    public const DEFAULT_TYPE = 'partial';

    private array $rawData = [];
    protected ?string $type = null;
    protected ?string $name = null;
    protected ?string $avatarUrl = null;

    public static function fromRawData(array $rawData): self
    {
        $type = isset($rawData['type']) ? (string) $rawData['type'] : self::DEFAULT_TYPE;

        $class = static::getMapClassFromType($type);

        /** @var static $resource */
        $resource = new $class();

        $resource
            ->setRawData($rawData)
            ->initialize();

        return $resource;
    }

    protected function setRawData(array $rawData): self
    {
        $this->rawData = $rawData;

        $this->object = (string) ($this->getRawData()['object'] ?? '');
        $this->id = (string) ($this->getRawData()['id'] ?? '');
        $this->type = isset($this->rawData['type']) ? (string) $this->rawData['type'] : null;
        $this->name = isset($this->rawData['name']) ? (string) $this->rawData['name'] : null;
        $this->avatarUrl = isset($this->rawData['avatar_url']) ? (string) $this->rawData['avatar_url'] : null;

        return $this;
    }

    protected static function getMapClassFromType(string $type): string
    {
        $typeFormatted = StringHelper::snakeCaseToCamelCase($type);
        $class = "Brd6\\NotionSdkPhp\\Resource\\User\\{$typeFormatted}User";

        if (!class_exists($class)) {
            throw new UnsupportedUserTypeException($type);
        }

        return $class;
    }

    abstract protected function initialize(): void;

    public function getRawData(): array
    {
        return $this->rawData;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAvatarUrl(): ?string
    {
        return $this->avatarUrl;
    }

    public function setAvatarUrl(?string $avatarUrl): self
    {
        $this->avatarUrl = $avatarUrl;

        return $this;
    }
}

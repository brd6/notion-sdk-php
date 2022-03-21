<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\User;

use Brd6\NotionSdkPhp\Resource\AbstractUser;
use Brd6\NotionSdkPhp\Resource\Property\PersonProperty;

class PersonUser extends AbstractUser
{
    public const RESOURCE_TYPE = 'person';

    protected ?PersonProperty $person = null;

    protected function initialize(): void
    {
        $this->person = PersonProperty::fromRawData((array) $this->getRawData()[(string) $this->getType()]);
    }

    public static function getResourceType(): string
    {
        return self::RESOURCE_TYPE;
    }

    public function getPerson(): ?PersonProperty
    {
        return $this->person;
    }

    public function setPerson(?PersonProperty $person): self
    {
        $this->person = $person;

        return $this;
    }
}

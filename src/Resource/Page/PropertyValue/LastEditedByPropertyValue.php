<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Page\PropertyValue;

use Brd6\NotionSdkPhp\Exception\UnsupportedUserTypeException;
use Brd6\NotionSdkPhp\Resource\User\AbstractUser;

class LastEditedByPropertyValue extends AbstractPropertyValue
{
    protected ?AbstractUser $lastEditedBy = null;

    /**
     * @throws UnsupportedUserTypeException
     */
    protected function initialize(): void
    {
        $this->lastEditedBy = AbstractUser::fromRawData((array) $this->getRawData()[$this->getType()]);
    }

    public function getLastEditedBy(): ?AbstractUser
    {
        return $this->lastEditedBy;
    }

    public function setLastEditedBy(?AbstractUser $lastEditedBy): self
    {
        $this->lastEditedBy = $lastEditedBy;

        return $this;
    }
}

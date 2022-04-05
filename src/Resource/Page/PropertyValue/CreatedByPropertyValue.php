<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Page\PropertyValue;

use Brd6\NotionSdkPhp\Exception\UnsupportedUserTypeException;
use Brd6\NotionSdkPhp\Resource\User\AbstractUser;

class CreatedByPropertyValue extends AbstractPropertyValue
{
    protected ?AbstractUser $createdBy = null;

    /**
     * @throws UnsupportedUserTypeException
     */
    protected function initialize(): void
    {
        $this->createdBy = AbstractUser::fromRawData((array) $this->getRawData()[$this->getType()]);
    }

    public function getCreatedBy(): ?AbstractUser
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?AbstractUser $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }
}

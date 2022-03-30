<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Page\PropertyItem;

use Brd6\NotionSdkPhp\Resource\User\AbstractUser;

class PeoplePropertyItem extends AbstractPropertyItem
{
    protected ?AbstractUser $people = null;

    protected function initialize(): void
    {
        $data = (array) $this->getRawData()[$this->getType()];
        $this->people = AbstractUser::fromRawData($data);
    }

    public function getPeople(): ?AbstractUser
    {
        return $this->people;
    }

    public function setPeople(AbstractUser $people): self
    {
        $this->people = $people;

        return $this;
    }
}

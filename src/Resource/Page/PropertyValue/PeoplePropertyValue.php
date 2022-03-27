<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Page\PropertyValue;

use Brd6\NotionSdkPhp\Resource\Page\AbstractPropertyValue;
use Brd6\NotionSdkPhp\Resource\User\AbstractUser;

use function array_map;

class PeoplePropertyValue extends AbstractPropertyValue
{
    /**
     * @var array|AbstractUser[]
     */
    protected array $people = [];

    protected function initialize(): void
    {
        $data = (array) $this->getRawData()[$this->getType()];

        $this->people = array_map(
            fn (array $peopleRawData) => AbstractUser::fromRawData($peopleRawData),
            $data,
        );
    }

    /**
     * @return array|AbstractUser[]
     */
    public function getPeople(): array
    {
        return $this->people;
    }

    /**
     * @param array|AbstractUser[] $people
     */
    public function setPeople(array $people): self
    {
        $this->people = $people;

        return $this;
    }
}

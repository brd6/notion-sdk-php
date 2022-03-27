<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Page\PropertyValue;

use Brd6\NotionSdkPhp\Resource\Page\AbstractPropertyValue;
use Brd6\NotionSdkPhp\Resource\Property\RelationProperty;

use function array_map;

class RelationPropertyValue extends AbstractPropertyValue
{
    /**
     * @var array|RelationProperty[]
     */
    protected array $relation = [];

    protected function initialize(): void
    {
        $data = (array) $this->getRawData()[$this->getType()];

        $this->relation = array_map(
            fn (array $relationRawData) => RelationProperty::fromRawData($relationRawData),
            $data,
        );
    }

    /**
     * @return array|RelationProperty[]
     */
    public function getRelation(): array
    {
        return $this->relation;
    }

    /**
     * @param array|RelationProperty[] $relation
     */
    public function setRelation(array $relation): self
    {
        $this->relation = $relation;

        return $this;
    }
}

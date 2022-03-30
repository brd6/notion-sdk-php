<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Page\PropertyItem;

use Brd6\NotionSdkPhp\Resource\Property\RelationProperty;

class RelationPropertyItem extends AbstractPropertyItem
{
    protected ?RelationProperty $relation = null;

    protected function initialize(): void
    {
        $data = (array) $this->getRawData()[$this->getType()];
        $this->relation = RelationProperty::fromRawData($data);
    }

    public function getRelation(): ?RelationProperty
    {
        return $this->relation;
    }

    public function setRelation(RelationProperty $relation): self
    {
        $this->relation = $relation;

        return $this;
    }
}

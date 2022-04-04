<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Database\PropertyObject;

use Brd6\NotionSdkPhp\Resource\Database\PropertyConfiguration\RelationPropertyConfiguration;

class RelationPropertyObject extends AbstractPropertyObject
{
    protected ?RelationPropertyConfiguration $relation = null;

    protected function initialize(): void
    {
        $data = (array) $this->getRawData()[$this->getType()];
        $this->relation = RelationPropertyConfiguration::fromRawData($data);
    }

    public function getRelation(): ?RelationPropertyConfiguration
    {
        return $this->relation;
    }

    public function setRelation(RelationPropertyConfiguration $relation): self
    {
        $this->relation = $relation;

        return $this;
    }
}

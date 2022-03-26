<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\RichText\Mention;

use Brd6\NotionSdkPhp\Resource\Property\PartialDatabaseProperty;
use Brd6\NotionSdkPhp\Resource\RichText\AbstractMention;

class DatabaseMention extends AbstractMention
{
    protected ?PartialDatabaseProperty $database = null;

    protected function initialize(): void
    {
        $data = (array) $this->getRawData()[$this->getType()];
        $this->database = PartialDatabaseProperty::fromRawData($data);
    }

    public function getDatabase(): ?PartialDatabaseProperty
    {
        return $this->database;
    }

    public function setDatabase(?PartialDatabaseProperty $database): self
    {
        $this->database = $database;

        return $this;
    }
}

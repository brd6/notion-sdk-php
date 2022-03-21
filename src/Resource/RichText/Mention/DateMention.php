<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\RichText\Mention;

use Brd6\NotionSdkPhp\Resource\Property\DateProperty;
use Brd6\NotionSdkPhp\Resource\RichText\AbstractMention;

class DateMention extends AbstractMention
{
    private ?DateProperty $date = null;

    protected function initialize(): void
    {
        $data = (array) $this->getRawData()[$this->getType()];
        $this->date = DateProperty::fromRawData($data);
    }

    public function getDate(): ?DateProperty
    {
        return $this->date;
    }

    public function setDate(?DateProperty $date): self
    {
        $this->date = $date;

        return $this;
    }
}

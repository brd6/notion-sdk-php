<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Block;

use Brd6\NotionSdkPhp\Resource\Property\EmbedProperty;

class EmbedBlock extends AbstractBlock
{
    protected ?EmbedProperty $embed = null;

    protected function initializeBlockProperty(): void
    {
        $data = (array) $this->getRawData()[$this->getType()];
        $this->embed = EmbedProperty::fromRawData($data);
    }

    public function getEmbed(): ?EmbedProperty
    {
        return $this->embed;
    }

    public function setEmbed(?EmbedProperty $embed): self
    {
        $this->embed = $embed;

        return $this;
    }
}

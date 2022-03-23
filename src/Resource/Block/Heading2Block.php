<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Block;

use Brd6\NotionSdkPhp\Resource\Property\HeadingProperty;

class Heading2Block extends AbstractHeadingBlock
{
    protected ?HeadingProperty $heading2 = null;

    public function getHeading2(): ?HeadingProperty
    {
        return $this->heading2;
    }

    public function setHeading2(?HeadingProperty $heading): self
    {
        $this->heading2 = $heading;

        return $this;
    }
}

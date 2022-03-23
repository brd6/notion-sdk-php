<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Block;

use Brd6\NotionSdkPhp\Resource\Property\HeadingProperty;

class Heading1Block extends AbstractHeadingBlock
{
    protected ?HeadingProperty $heading1 = null;

    public function getHeading1(): ?HeadingProperty
    {
        return $this->heading1;
    }

    public function setHeading1(?HeadingProperty $heading): self
    {
        $this->heading1 = $heading;

        return $this;
    }
}

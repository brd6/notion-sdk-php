<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Block;

use Brd6\NotionSdkPhp\Resource\Property\HeadingProperty;

class Heading5Block extends AbstractHeadingBlock
{
    protected ?HeadingProperty $heading5 = null;

    public function getHeading5(): ?HeadingProperty
    {
        return $this->heading5;
    }

    public function setHeading5(?HeadingProperty $heading): self
    {
        $this->heading5 = $heading;

        return $this;
    }
}

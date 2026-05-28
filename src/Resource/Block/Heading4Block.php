<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Block;

use Brd6\NotionSdkPhp\Resource\Property\HeadingProperty;

class Heading4Block extends AbstractHeadingBlock
{
    protected ?HeadingProperty $heading4 = null;

    public function getHeading4(): ?HeadingProperty
    {
        return $this->heading4;
    }

    public function setHeading4(?HeadingProperty $heading): self
    {
        $this->heading4 = $heading;

        return $this;
    }
}

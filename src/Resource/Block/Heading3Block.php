<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Block;

use Brd6\NotionSdkPhp\Resource\Property\HeadingProperty;

class Heading3Block extends AbstractHeadingBlock
{
    protected ?HeadingProperty $heading3 = null;

    public function getHeading3(): ?HeadingProperty
    {
        return $this->heading3;
    }

    public function setHeading3(?HeadingProperty $heading): self
    {
        $this->heading3 = $heading;

        return $this;
    }
}

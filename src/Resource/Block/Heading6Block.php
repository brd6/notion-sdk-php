<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Block;

use Brd6\NotionSdkPhp\Resource\Property\HeadingProperty;

class Heading6Block extends AbstractHeadingBlock
{
    protected ?HeadingProperty $heading6 = null;

    public function getHeading6(): ?HeadingProperty
    {
        return $this->heading6;
    }

    public function setHeading6(?HeadingProperty $heading): self
    {
        $this->heading6 = $heading;

        return $this;
    }
}

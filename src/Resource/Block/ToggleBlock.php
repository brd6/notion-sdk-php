<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Block;

use Brd6\NotionSdkPhp\Exception\InvalidResourceException;
use Brd6\NotionSdkPhp\Exception\InvalidResourceTypeException;
use Brd6\NotionSdkPhp\Exception\InvalidRichTextException;
use Brd6\NotionSdkPhp\Exception\UnsupportedRichTextTypeException;
use Brd6\NotionSdkPhp\Resource\AbstractBlock;
use Brd6\NotionSdkPhp\Resource\Property\ToggleProperty;

class ToggleBlock extends AbstractBlock
{
    protected ?ToggleProperty $toggle = null;

    /**
     * @throws InvalidResourceException
     * @throws InvalidResourceTypeException
     * @throws InvalidRichTextException
     * @throws UnsupportedRichTextTypeException
     */
    protected function initializeBlockProperty(): void
    {
        $data = (array) $this->getRawData()[$this->getType()];
        $this->toggle = ToggleProperty::fromRawData($data);
    }

    public function getToggle(): ?ToggleProperty
    {
        return $this->toggle;
    }

    public function setToggle(?ToggleProperty $toggle): self
    {
        $this->toggle = $toggle;

        return $this;
    }
}

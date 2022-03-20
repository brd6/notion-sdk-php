<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Block;

use Brd6\NotionSdkPhp\Exception\InvalidResourceException;
use Brd6\NotionSdkPhp\Exception\InvalidResourceTypeException;
use Brd6\NotionSdkPhp\Exception\InvalidRichTextException;
use Brd6\NotionSdkPhp\Exception\UnsupportedRichTextTypeException;
use Brd6\NotionSdkPhp\Resource\AbstractBlock;
use Brd6\NotionSdkPhp\Resource\Property\CalloutProperty;

class CalloutBlock extends AbstractBlock
{
    protected ?CalloutProperty $callout = null;

    /**
     * @throws InvalidResourceTypeException
     * @throws InvalidRichTextException
     * @throws UnsupportedRichTextTypeException
     * @throws InvalidResourceException
     */
    protected function initializeBlockProperty(): void
    {
        $data = (array) $this->getRawData()[$this->getType()];
        $this->callout = CalloutProperty::fromRawData($data);
    }

    public function getCallout(): ?CalloutProperty
    {
        return $this->callout;
    }

    /**
     * @param CalloutProperty|null $callout
     *
     * @return CalloutBlock
     */
    public function setCallout(?CalloutProperty $callout): self
    {
        $this->callout = $callout;

        return $this;
    }
}
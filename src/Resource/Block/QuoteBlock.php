<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Block;

use Brd6\NotionSdkPhp\Exception\InvalidResourceException;
use Brd6\NotionSdkPhp\Exception\InvalidResourceTypeException;
use Brd6\NotionSdkPhp\Exception\InvalidRichTextException;
use Brd6\NotionSdkPhp\Exception\UnsupportedRichTextTypeException;
use Brd6\NotionSdkPhp\Exception\UnsupportedUserTypeException;
use Brd6\NotionSdkPhp\Resource\Property\QuoteProperty;

class QuoteBlock extends AbstractBlock
{
    protected ?QuoteProperty $quote = null;

    /**
     * @throws InvalidResourceException
     * @throws InvalidResourceTypeException
     * @throws InvalidRichTextException
     * @throws UnsupportedRichTextTypeException
     * @throws UnsupportedUserTypeException
     */
    protected function initializeBlockProperty(): void
    {
        $data = (array) $this->getRawData()[$this->getType()];

        /** @var QuoteProperty $property */
        $property = QuoteProperty::fromRawData($data);

        $this->quote = $property;
    }

    public function getQuote(): ?QuoteProperty
    {
        return $this->quote;
    }

    /**
     * @param QuoteProperty|null $quote
     *
     * @return QuoteBlock
     */
    public function setQuote(?QuoteProperty $quote): self
    {
        $this->quote = $quote;

        return $this;
    }
}

<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Block;

use Brd6\NotionSdkPhp\Exception\InvalidResourceException;
use Brd6\NotionSdkPhp\Exception\InvalidResourceTypeException;
use Brd6\NotionSdkPhp\Exception\InvalidRichTextException;
use Brd6\NotionSdkPhp\Exception\UnsupportedRichTextTypeException;
use Brd6\NotionSdkPhp\Resource\Property\TemplateProperty;

class TemplateBlock extends AbstractBlock
{
    protected ?TemplateProperty $template = null;

    /**
     * @throws InvalidResourceException
     * @throws InvalidResourceTypeException
     * @throws InvalidRichTextException
     * @throws UnsupportedRichTextTypeException
     */
    protected function initializeBlockProperty(): void
    {
        $data = (array) $this->getRawData()[$this->getType()];

        /** @var TemplateProperty $property */
        $property = TemplateProperty::fromRawData($data);

        $this->template = $property;
    }

    public function getTemplate(): ?TemplateProperty
    {
        return $this->template;
    }

    public function setTemplate(?TemplateProperty $template): self
    {
        $this->template = $template;

        return $this;
    }
}

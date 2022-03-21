<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Block;

use Brd6\NotionSdkPhp\Exception\InvalidRichTextException;
use Brd6\NotionSdkPhp\Exception\UnsupportedRichTextTypeException;
use Brd6\NotionSdkPhp\Resource\AbstractBlock;
use Brd6\NotionSdkPhp\Resource\Property\CodeProperty;

class CodeBlock extends AbstractBlock
{
    protected ?CodeProperty $code = null;

    /**
     * @throws InvalidRichTextException
     * @throws UnsupportedRichTextTypeException
     */
    protected function initializeBlockProperty(): void
    {
        $data = (array) $this->getRawData()[$this->getType()];
        $this->code = CodeProperty::fromRawData($data);
    }

    public function getCode(): ?CodeProperty
    {
        return $this->code;
    }

    public function setCode(?CodeProperty $code): self
    {
        $this->code = $code;

        return $this;
    }
}

<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Page\PropertyValue;

use Brd6\NotionSdkPhp\Exception\InvalidRichTextException;
use Brd6\NotionSdkPhp\Exception\UnsupportedRichTextTypeException;
use Brd6\NotionSdkPhp\Resource\RichText\AbstractRichText;

use function array_map;

class StatusPropertyValue extends AbstractPropertyValue
{
    /**
     * @var array|AbstractRichText[]
     */
    protected array $status = [];

    /**
     * @throws InvalidRichTextException
     * @throws UnsupportedRichTextTypeException
     */
    protected function initialize(): void
    {
        $this->status = ($this->getRawData()[$this->getType()] ?? '');
    }

    /**
     * @return array|AbstractRichText[]
     */
    public function getStatus(): array
    {
        return $this->status;
    }

    /**
     * @param array|AbstractRichText[] $status
     */
    public function setTtatus(array $status): self
    {
        $this->status = $status;

        return $this;
    }
}

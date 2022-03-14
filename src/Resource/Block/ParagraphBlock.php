<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Block;

use Brd6\NotionSdkPhp\Resource\AbstractBlock;
use Brd6\NotionSdkPhp\Resource\Property\ParagraphProperty;

class ParagraphBlock extends AbstractBlock
{
    protected ?ParagraphProperty $paragraph = null;

    protected function initializeBlockProperty(): void
    {
        $data = (array) $this->getRawData()[$this->getType()];
        $this->paragraph = ParagraphProperty::fromRawData($data);
    }

    public function getParagraph(): ?ParagraphProperty
    {
        return $this->paragraph;
    }

    /**
     * @param ParagraphProperty|null $paragraph
     *
     * @return ParagraphBlock
     */
    public function setParagraph(?ParagraphProperty $paragraph): self
    {
        $this->paragraph = $paragraph;

        return $this;
    }
}

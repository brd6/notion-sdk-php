<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Block;

use Brd6\NotionSdkPhp\Exception\InvalidRichTextException;
use Brd6\NotionSdkPhp\Exception\UnsupportedRichTextTypeException;
use Brd6\NotionSdkPhp\Resource\Property\MeetingNotesProperty;

class TranscriptionBlock extends AbstractBlock
{
    protected ?MeetingNotesProperty $transcription = null;

    /**
     * The payload's `children` key holds block-id references, not inline child blocks.
     */
    protected function initializeChildren(): void
    {
    }

    /**
     * @throws InvalidRichTextException
     * @throws UnsupportedRichTextTypeException
     */
    protected function initializeBlockProperty(): void
    {
        $data = (array) $this->getRawData()[$this->getType()];
        $this->transcription = MeetingNotesProperty::fromRawData($data);
    }

    public function getTranscription(): ?MeetingNotesProperty
    {
        return $this->transcription;
    }

    public function setTranscription(?MeetingNotesProperty $transcription): self
    {
        $this->transcription = $transcription;

        return $this;
    }
}

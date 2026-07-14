<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Block;

use Brd6\NotionSdkPhp\Exception\InvalidRichTextException;
use Brd6\NotionSdkPhp\Exception\UnsupportedRichTextTypeException;
use Brd6\NotionSdkPhp\Resource\Property\MeetingNotesProperty;

/**
 * The pre-2026-03-11 name of the meeting notes block: responses on older API
 * versions carry `transcription` with the same payload shape. Read-only at
 * the Notion API.
 */
class TranscriptionBlock extends AbstractBlock
{
    protected ?MeetingNotesProperty $transcription = null;

    /**
     * The payload's `children` key holds block-id references (summary, notes,
     * transcript), not inline child blocks; fetch actual children through
     * blocks()->children()->list().
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

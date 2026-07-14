<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Block;

use Brd6\NotionSdkPhp\Exception\InvalidRichTextException;
use Brd6\NotionSdkPhp\Exception\UnsupportedRichTextTypeException;
use Brd6\NotionSdkPhp\Resource\Property\MeetingNotesProperty;

class MeetingNotesBlock extends AbstractBlock
{
    protected ?MeetingNotesProperty $meetingNotes = null;

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
        $this->meetingNotes = MeetingNotesProperty::fromRawData($data);
    }

    public function getMeetingNotes(): ?MeetingNotesProperty
    {
        return $this->meetingNotes;
    }

    public function setMeetingNotes(?MeetingNotesProperty $meetingNotes): self
    {
        $this->meetingNotes = $meetingNotes;

        return $this;
    }
}

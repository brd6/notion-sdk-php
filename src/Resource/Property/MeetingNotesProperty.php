<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Property;

use Brd6\NotionSdkPhp\Exception\InvalidRichTextException;
use Brd6\NotionSdkPhp\Exception\UnsupportedRichTextTypeException;
use Brd6\NotionSdkPhp\Resource\RichText\AbstractRichText;

use function array_map;

class MeetingNotesProperty extends AbstractProperty
{
    public const STATUS_TRANSCRIPTION_NOT_STARTED = 'transcription_not_started';
    public const STATUS_TRANSCRIPTION_PAUSED = 'transcription_paused';
    public const STATUS_TRANSCRIPTION_IN_PROGRESS = 'transcription_in_progress';
    public const STATUS_TRANSCRIPTION_FAILED = 'transcription_failed';
    public const STATUS_SUMMARY_IN_PROGRESS = 'summary_in_progress';
    public const STATUS_NOTES_READY = 'notes_ready';

    /**
     * @var array|AbstractRichText[]
     */
    protected array $title = [];
    protected string $status = '';
    protected array $children = [];
    protected array $calendarEvent = [];
    protected array $recording = [];

    /**
     * @param array $rawData
     *
     * @return MeetingNotesProperty
     *
     * @throws InvalidRichTextException
     * @throws UnsupportedRichTextTypeException
     */
    public static function fromRawData(array $rawData): self
    {
        $property = new self();

        $property->title = isset($rawData['title']) ? array_map(
            fn (array $richTextRawData) => AbstractRichText::fromRawData($richTextRawData),
            (array) $rawData['title'],
        ) : [];
        $property->status = (string) ($rawData['status'] ?? '');
        $property->children = (array) ($rawData['children'] ?? []);
        $property->calendarEvent = (array) ($rawData['calendar_event'] ?? []);
        $property->recording = (array) ($rawData['recording'] ?? []);

        return $property;
    }

    /**
     * @return array|AbstractRichText[]
     */
    public function getTitle(): array
    {
        return $this->title;
    }

    /**
     * @param array|AbstractRichText[] $title
     */
    public function setTitle(array $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getChildren(): array
    {
        return $this->children;
    }

    public function setChildren(array $children): self
    {
        $this->children = $children;

        return $this;
    }

    public function getCalendarEvent(): array
    {
        return $this->calendarEvent;
    }

    public function setCalendarEvent(array $calendarEvent): self
    {
        $this->calendarEvent = $calendarEvent;

        return $this;
    }

    public function getRecording(): array
    {
        return $this->recording;
    }

    public function setRecording(array $recording): self
    {
        $this->recording = $recording;

        return $this;
    }
}

<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource;

use Brd6\NotionSdkPhp\Exception\InvalidFileException;
use Brd6\NotionSdkPhp\Exception\InvalidParentException;
use Brd6\NotionSdkPhp\Exception\InvalidRichTextException;
use Brd6\NotionSdkPhp\Exception\UnsupportedFileTypeException;
use Brd6\NotionSdkPhp\Exception\UnsupportedParentTypeException;
use Brd6\NotionSdkPhp\Exception\UnsupportedRichTextTypeException;
use Brd6\NotionSdkPhp\Exception\UnsupportedUserTypeException;
use Brd6\NotionSdkPhp\Resource\Page\Parent\AbstractParentProperty;
use Brd6\NotionSdkPhp\Resource\RichText\AbstractRichText;
use Brd6\NotionSdkPhp\Resource\User\AbstractUser;
use DateTimeImmutable;

class Comment extends AbstractResource
{
    public const RESOURCE_TYPE = 'comment';

    protected ?AbstractParentProperty $parent = null;
    protected string $discussionId = '';
    protected ?DateTimeImmutable $createdTime = null;
    protected ?DateTimeImmutable $lastEditedTime = null;
    protected ?UserInterface $createdBy = null;

    /**
     * @var array<AbstractRichText>
     */
    protected array $richText = [];

    /**
     * @var array<CommentAttachment>
     */
    protected array $attachments = [];

    public function __construct()
    {
        parent::__construct();

        $this->object = self::RESOURCE_TYPE;
    }

    /**
     * @throws InvalidFileException
     * @throws InvalidParentException
     * @throws InvalidRichTextException
     * @throws UnsupportedFileTypeException
     * @throws UnsupportedParentTypeException
     * @throws UnsupportedRichTextTypeException
     * @throws UnsupportedUserTypeException
     */
    protected function initialize(): void
    {
        $this->parent = isset($this->getRawData()['parent']) ?
            AbstractParentProperty::fromRawData((array) $this->getRawData()['parent']) :
            null;

        $this->discussionId = (string) ($this->getRawData()['discussion_id'] ?? '');

        $this->createdTime = isset($this->getRawData()['created_time']) ?
            new DateTimeImmutable((string) $this->getRawData()['created_time']) :
            null;

        $this->lastEditedTime = isset($this->getRawData()['last_edited_time']) ?
            new DateTimeImmutable((string) $this->getRawData()['last_edited_time']) :
            null;

        $this->createdBy = isset($this->getRawData()['created_by']) ?
            AbstractUser::fromRawData((array) $this->getRawData()['created_by']) :
            null;

        /** @var array<array> $richTextData */
        $richTextData = (array) ($this->getRawData()['rich_text'] ?? []);
        foreach ($richTextData as $richTextItem) {
            $this->richText[] = AbstractRichText::fromRawData($richTextItem);
        }

        /** @var array<array> $attachmentsData */
        $attachmentsData = (array) ($this->getRawData()['attachments'] ?? []);
        foreach ($attachmentsData as $attachmentItem) {
            $this->attachments[] = CommentAttachment::fromRawData($attachmentItem);
        }
    }

    public static function getResourceType(): string
    {
        return self::RESOURCE_TYPE;
    }

    public function getParent(): ?AbstractParentProperty
    {
        return $this->parent;
    }

    public function setParent(?AbstractParentProperty $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    public function getDiscussionId(): string
    {
        return $this->discussionId;
    }

    public function setDiscussionId(string $discussionId): self
    {
        $this->discussionId = $discussionId;

        return $this;
    }

    public function getCreatedTime(): ?DateTimeImmutable
    {
        return $this->createdTime;
    }

    public function setCreatedTime(?DateTimeImmutable $createdTime): self
    {
        $this->createdTime = $createdTime;

        return $this;
    }

    public function getLastEditedTime(): ?DateTimeImmutable
    {
        return $this->lastEditedTime;
    }

    public function setLastEditedTime(?DateTimeImmutable $lastEditedTime): self
    {
        $this->lastEditedTime = $lastEditedTime;

        return $this;
    }

    public function getCreatedBy(): ?UserInterface
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?UserInterface $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * @return array<AbstractRichText>
     */
    public function getRichText(): array
    {
        return $this->richText;
    }

    /**
     * @param array<AbstractRichText> $richText
     */
    public function setRichText(array $richText): self
    {
        $this->richText = $richText;

        return $this;
    }

    /**
     * @return array<CommentAttachment>
     */
    public function getAttachments(): array
    {
        return $this->attachments;
    }

    /**
     * @param array<CommentAttachment> $attachments
     */
    public function setAttachments(array $attachments): self
    {
        $this->attachments = $attachments;

        return $this;
    }
}

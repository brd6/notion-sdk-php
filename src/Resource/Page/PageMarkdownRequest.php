<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Page;

use Brd6\NotionSdkPhp\Resource\AbstractJsonSerializable;

use function array_filter;

class PageMarkdownRequest extends AbstractJsonSerializable
{
    public const TYPE_UPDATE_CONTENT = 'update_content';
    public const TYPE_REPLACE_CONTENT = 'replace_content';
    public const TYPE_INSERT_CONTENT = 'insert_content';
    public const TYPE_REPLACE_CONTENT_RANGE = 'replace_content_range';

    public const POSITION_START = 'start';
    public const POSITION_END = 'end';

    protected ?string $type = null;
    protected array $updateContent = [];
    protected array $replaceContent = [];
    protected array $insertContent = [];
    protected array $replaceContentRange = [];

    /**
     * @param array $contentUpdates array of operations: {old_str, new_str, replace_all_matches?}, max 100
     */
    public static function updateContent(array $contentUpdates, bool $allowDeletingContent = false): self
    {
        return (new self())
            ->setType(self::TYPE_UPDATE_CONTENT)
            ->setUpdateContent(array_filter([
                'content_updates' => $contentUpdates,
                'allow_deleting_content' => $allowDeletingContent ?: null,
            ]));
    }

    public static function replaceContent(string $newStr, bool $allowDeletingContent = false): self
    {
        return (new self())
            ->setType(self::TYPE_REPLACE_CONTENT)
            ->setReplaceContent(array_filter([
                'new_str' => $newStr,
                'allow_deleting_content' => $allowDeletingContent ?: null,
            ]));
    }

    /**
     * @param string|null $position self::POSITION_START or self::POSITION_END; mutually exclusive with $after
     * @param string|null $after an ellipsis selection ("start text...end text") to insert after
     */
    public static function insertContent(string $content, ?string $position = null, ?string $after = null): self
    {
        return (new self())
            ->setType(self::TYPE_INSERT_CONTENT)
            ->setInsertContent(array_filter([
                'content' => $content,
                'position' => $position !== null ? ['type' => $position] : null,
                'after' => $after,
            ]));
    }

    /**
     * @param string $contentRange an ellipsis selection ("start text...end text") of the content to replace
     */
    public static function replaceContentRange(
        string $content,
        string $contentRange,
        bool $allowDeletingContent = false
    ): self {
        return (new self())
            ->setType(self::TYPE_REPLACE_CONTENT_RANGE)
            ->setReplaceContentRange(array_filter([
                'content' => $content,
                'content_range' => $contentRange,
                'allow_deleting_content' => $allowDeletingContent ?: null,
            ]));
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getUpdateContent(): array
    {
        return $this->updateContent;
    }

    public function setUpdateContent(array $updateContent): self
    {
        $this->updateContent = $updateContent;

        return $this;
    }

    public function getReplaceContent(): array
    {
        return $this->replaceContent;
    }

    public function setReplaceContent(array $replaceContent): self
    {
        $this->replaceContent = $replaceContent;

        return $this;
    }

    public function getInsertContent(): array
    {
        return $this->insertContent;
    }

    public function setInsertContent(array $insertContent): self
    {
        $this->insertContent = $insertContent;

        return $this;
    }

    public function getReplaceContentRange(): array
    {
        return $this->replaceContentRange;
    }

    public function setReplaceContentRange(array $replaceContentRange): self
    {
        $this->replaceContentRange = $replaceContentRange;

        return $this;
    }
}

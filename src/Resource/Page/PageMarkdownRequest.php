<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Page;

use Brd6\NotionSdkPhp\Resource\AbstractJsonSerializable;

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
        $updateContent = ['content_updates' => $contentUpdates];

        if ($allowDeletingContent) {
            $updateContent['allow_deleting_content'] = true;
        }

        return (new self())
            ->setType(self::TYPE_UPDATE_CONTENT)
            ->setUpdateContent($updateContent);
    }

    public static function replaceContent(string $newStr, bool $allowDeletingContent = false): self
    {
        $replaceContent = ['new_str' => $newStr];

        if ($allowDeletingContent) {
            $replaceContent['allow_deleting_content'] = true;
        }

        return (new self())
            ->setType(self::TYPE_REPLACE_CONTENT)
            ->setReplaceContent($replaceContent);
    }

    /**
     * @param string|null $position self::POSITION_START or self::POSITION_END; mutually exclusive with $after
     * @param string|null $after an ellipsis selection ("start text...end text") to insert after
     */
    public static function insertContent(string $content, ?string $position = null, ?string $after = null): self
    {
        $insertContent = ['content' => $content];

        if ($position !== null) {
            $insertContent['position'] = ['type' => $position];
        }

        if ($after !== null) {
            $insertContent['after'] = $after;
        }

        return (new self())
            ->setType(self::TYPE_INSERT_CONTENT)
            ->setInsertContent($insertContent);
    }

    /**
     * @param string $contentRange an ellipsis selection ("start text...end text") of the content to replace
     */
    public static function replaceContentRange(
        string $content,
        string $contentRange,
        bool $allowDeletingContent = false
    ): self {
        $replaceContentRange = [
            'content' => $content,
            'content_range' => $contentRange,
        ];

        if ($allowDeletingContent) {
            $replaceContentRange['allow_deleting_content'] = true;
        }

        return (new self())
            ->setType(self::TYPE_REPLACE_CONTENT_RANGE)
            ->setReplaceContentRange($replaceContentRange);
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

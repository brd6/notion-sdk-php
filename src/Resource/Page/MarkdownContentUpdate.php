<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Page;

class MarkdownContentUpdate
{
    protected string $oldStr;
    protected string $newStr;
    protected ?bool $replaceAllMatches;

    public function __construct(string $oldStr, string $newStr, ?bool $replaceAllMatches = null)
    {
        $this->oldStr = $oldStr;
        $this->newStr = $newStr;
        $this->replaceAllMatches = $replaceAllMatches;
    }

    public function getOldStr(): string
    {
        return $this->oldStr;
    }

    public function getNewStr(): string
    {
        return $this->newStr;
    }

    public function getReplaceAllMatches(): ?bool
    {
        return $this->replaceAllMatches;
    }

    public function toArray(): array
    {
        $data = [
            'old_str' => $this->oldStr,
            'new_str' => $this->newStr,
        ];

        if ($this->replaceAllMatches !== null) {
            $data['replace_all_matches'] = $this->replaceAllMatches;
        }

        return $data;
    }
}

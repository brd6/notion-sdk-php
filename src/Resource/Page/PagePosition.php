<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Page;

class PagePosition
{
    public const TYPE_AFTER_BLOCK = 'after_block';
    public const TYPE_PAGE_START = 'page_start';
    public const TYPE_PAGE_END = 'page_end';

    protected string $type;
    protected ?string $afterBlockId;

    private function __construct(string $type, ?string $afterBlockId = null)
    {
        $this->type = $type;
        $this->afterBlockId = $afterBlockId;
    }

    public static function afterBlock(string $blockId): self
    {
        return new self(self::TYPE_AFTER_BLOCK, $blockId);
    }

    public static function pageStart(): self
    {
        return new self(self::TYPE_PAGE_START);
    }

    public static function pageEnd(): self
    {
        return new self(self::TYPE_PAGE_END);
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getAfterBlockId(): ?string
    {
        return $this->afterBlockId;
    }

    public function toArray(): array
    {
        $data = ['type' => $this->type];

        if ($this->afterBlockId !== null) {
            $data['after_block'] = ['id' => $this->afterBlockId];
        }

        return $data;
    }
}

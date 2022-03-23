<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\File;

class Emoji extends AbstractFile
{
    public const FILE_TYPE = 'emoji';

    protected string $emoji = '';

    public static function getFileType(): string
    {
        return self::FILE_TYPE;
    }

    protected function initialize(): void
    {
        $this->emoji = (string) $this->getRawData()[$this->getType()];
    }

    public function getEmoji(): string
    {
        return $this->emoji;
    }

    public function setEmoji(string $emoji): self
    {
        $this->emoji = $emoji;

        return $this;
    }
}

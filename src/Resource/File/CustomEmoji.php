<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\File;

use Brd6\NotionSdkPhp\Resource\Property\CustomEmojiProperty;

class CustomEmoji extends AbstractFile
{
    public const FILE_TYPE = 'custom_emoji';

    protected ?CustomEmojiProperty $customEmoji = null;

    public static function getFileType(): string
    {
        return self::FILE_TYPE;
    }

    protected function initialize(): void
    {
        $data = (array) $this->getRawData()[$this->getType()];
        $this->customEmoji = CustomEmojiProperty::fromRawData($data);
    }

    public function getCustomEmoji(): ?CustomEmojiProperty
    {
        return $this->customEmoji;
    }

    public function setCustomEmoji(CustomEmojiProperty $customEmoji): self
    {
        $this->customEmoji = $customEmoji;

        return $this;
    }
}

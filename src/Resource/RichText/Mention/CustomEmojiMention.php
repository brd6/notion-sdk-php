<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\RichText\Mention;

use Brd6\NotionSdkPhp\Resource\RichText\AbstractMention;
use Brd6\NotionSdkPhp\Resource\Property\CustomEmojiProperty;

class CustomEmojiMention extends AbstractMention
{
    protected ?CustomEmojiProperty $customEmoji = null;

    protected function initialize(): void
    {
        $data = (array) $this->getRawData()[$this->getType()];
        $this->customEmoji = CustomEmojiProperty::fromRawData($data);
    }

    public function getCustomEmoji(): ?CustomEmojiProperty
    {
        return $this->customEmoji;
    }

    public function setCustomEmoji(?CustomEmojiProperty $customEmoji): self
    {
        $this->customEmoji = $customEmoji;

        return $this;
    }
} 
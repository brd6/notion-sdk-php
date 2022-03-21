<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\RichText;

use Brd6\NotionSdkPhp\Resource\AbstractRichText;

class Mention extends AbstractRichText
{
    public const RICH_TEXT_TYPE = 'mention';
    protected string $type = '';
    protected ?MentionInterface $mention = null;

    public function __construct()
    {
        $this->type = self::RICH_TEXT_TYPE;
    }

    protected function initialize(): void
    {
        $data = (array) $this->getRawData()[$this->getType()];
        $this->mention = AbstractMention::fromRawData($data);
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getMention(): ?MentionInterface
    {
        return $this->mention;
    }

    public function setMention(MentionInterface $mention): self
    {
        $this->mention = $mention;

        return $this;
    }
}

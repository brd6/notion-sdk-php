<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\RichText;

use Brd6\NotionSdkPhp\Resource\Annotations;
use Brd6\NotionSdkPhp\Resource\Property\TextProperty;

class Text extends AbstractRichText
{
    public const RICH_TEXT_TYPE = 'text';
    protected ?TextProperty $text = null;

    public function __construct()
    {
        $this->type = self::RICH_TEXT_TYPE;
        $this->annotations = new Annotations();
    }

    public static function fromContent(string $content): self
    {
        return (new self())->setText((new TextProperty())->setContent($content));
    }

    protected function initialize(): void
    {
        $data = (array) $this->getRawData()[$this->getType()];
        $this->text = TextProperty::fromRawData($data);
    }

    public function getText(): ?TextProperty
    {
        return $this->text;
    }

    public function setText(?TextProperty $text): self
    {
        $this->text = $text;

        return $this;
    }
}

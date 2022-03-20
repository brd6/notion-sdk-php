<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\RichText;

use Brd6\NotionSdkPhp\Resource\AbstractRichText;
use Brd6\NotionSdkPhp\Resource\Link;

class Mention extends AbstractRichText
{
    public const RICH_TEXT_TYPE = 'mention';

    protected string $content = '';
    protected ?Link $link = null;

    public function __construct()
    {
        $this->type = self::RICH_TEXT_TYPE;
    }

    protected function initialize(): void
    {
        $data = (array) $this->getRawData()[$this->getType()];
        $this->content = (string) $data['content'];
        $this->link = $data['link'] !== null ? Link::fromRawData((array) $data['link']) : null;
    }
}

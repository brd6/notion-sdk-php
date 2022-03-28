<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource;

use Brd6\NotionSdkPhp\Constant\ColorConstant;

class Annotations extends AbstractJsonSerializable
{
    protected bool $bold = false;
    protected bool $italic = false;
    protected bool $strikethrough = false;
    protected bool $underline = false;
    protected bool $code = false;
    protected string $color = '';

    public function __construct()
    {
        $this->color = ColorConstant::DEFAULT;
    }

    public static function fromRawData(array $rawData): self
    {
        $annotations = new self();
        $annotations->bold = (bool) $rawData['bold'];
        $annotations->italic = (bool) $rawData['italic'];
        $annotations->strikethrough = (bool) $rawData['strikethrough'];
        $annotations->underline = (bool) $rawData['underline'];
        $annotations->code = (bool) $rawData['code'];
        $annotations->color = (string) $rawData['color'];

        return $annotations;
    }

    public function isBold(): bool
    {
        return $this->bold;
    }

    public function setBold(bool $bold): Annotations
    {
        $this->bold = $bold;

        return $this;
    }

    public function isItalic(): bool
    {
        return $this->italic;
    }

    public function setItalic(bool $italic): Annotations
    {
        $this->italic = $italic;

        return $this;
    }

    public function isStrikethrough(): bool
    {
        return $this->strikethrough;
    }

    public function setStrikethrough(bool $strikethrough): Annotations
    {
        $this->strikethrough = $strikethrough;

        return $this;
    }

    public function isUnderline(): bool
    {
        return $this->underline;
    }

    public function setUnderline(bool $underline): Annotations
    {
        $this->underline = $underline;

        return $this;
    }

    public function isCode(): bool
    {
        return $this->code;
    }

    public function setCode(bool $code): Annotations
    {
        $this->code = $code;

        return $this;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function setColor(string $color): Annotations
    {
        $this->color = $color;

        return $this;
    }
}

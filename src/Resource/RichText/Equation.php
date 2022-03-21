<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\RichText;

use Brd6\NotionSdkPhp\Resource\AbstractRichText;
use Brd6\NotionSdkPhp\Resource\Property\EquationProperty;

class Equation extends AbstractRichText
{
    public const RICH_TEXT_TYPE = 'equation';
    protected ?EquationProperty $equation = null;

    public function __construct()
    {
        $this->type = self::RICH_TEXT_TYPE;
    }

    protected function initialize(): void
    {
        $data = (array) $this->getRawData()[$this->getType()];
        $this->equation = EquationProperty::fromRawData($data);
    }

    public function getEquation(): ?EquationProperty
    {
        return $this->equation;
    }

    public function setEquation(?EquationProperty $text): self
    {
        $this->equation = $text;

        return $this;
    }
}

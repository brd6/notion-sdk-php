<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Property;

use Brd6\NotionSdkPhp\Exception\InvalidResourceException;
use Brd6\NotionSdkPhp\Exception\InvalidResourceTypeException;
use Brd6\NotionSdkPhp\Exception\InvalidRichTextException;
use Brd6\NotionSdkPhp\Exception\UnsupportedRichTextTypeException;
use Brd6\NotionSdkPhp\Exception\UnsupportedUserTypeException;

class HeadingProperty extends AbstractParagraphProperty
{
    protected ?bool $isToggleable = null;

    /**
     * @throws InvalidResourceException
     * @throws InvalidResourceTypeException
     * @throws InvalidRichTextException
     * @throws UnsupportedRichTextTypeException
     * @throws UnsupportedUserTypeException
     */
    public static function fromRawData(array $rawData): self
    {
        /** @var self $property */
        $property = parent::fromRawData($rawData);

        $property->isToggleable = isset($rawData['is_toggleable']) ? (bool) $rawData['is_toggleable'] : null;

        return $property;
    }

    public function isToggleable(): bool
    {
        return $this->isToggleable ?? false;
    }

    public function setToggleable(bool $isToggleable): self
    {
        $this->isToggleable = $isToggleable;

        return $this;
    }
}

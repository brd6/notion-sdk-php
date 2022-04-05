<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Block;

use Brd6\NotionSdkPhp\Exception\InvalidResourceException;
use Brd6\NotionSdkPhp\Exception\InvalidResourceTypeException;
use Brd6\NotionSdkPhp\Exception\InvalidRichTextException;
use Brd6\NotionSdkPhp\Exception\UnsupportedRichTextTypeException;
use Brd6\NotionSdkPhp\Exception\UnsupportedUserTypeException;
use Brd6\NotionSdkPhp\Resource\Property\BulletedListItemProperty;

class BulletedListItemBlock extends AbstractBlock
{
    protected ?BulletedListItemProperty $bulletedListItem = null;

    /**
     * @throws InvalidResourceException
     * @throws InvalidResourceTypeException
     * @throws InvalidRichTextException
     * @throws UnsupportedRichTextTypeException
     * @throws UnsupportedUserTypeException
     */
    protected function initializeBlockProperty(): void
    {
        $data = (array) $this->getRawData()[$this->getType()];

        /** @var BulletedListItemProperty $property */
        $property = BulletedListItemProperty::fromRawData($data);

        $this->bulletedListItem = $property;
    }

    public function getBulletedListItem(): ?BulletedListItemProperty
    {
        return $this->bulletedListItem;
    }

    public function setBulletedListItem(?BulletedListItemProperty $bulletedListItem): self
    {
        $this->bulletedListItem = $bulletedListItem;

        return $this;
    }
}

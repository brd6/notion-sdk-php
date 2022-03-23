<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Block;

use Brd6\NotionSdkPhp\Resource\Property\BreadcrumbProperty;

class BreadcrumbBlock extends AbstractBlock
{
    protected ?BreadcrumbProperty $breadcrumb = null;

    protected function initializeBlockProperty(): void
    {
        $data = (array) $this->getRawData()[$this->getType()];
        $this->breadcrumb = BreadcrumbProperty::fromRawData($data);
    }

    public function getBreadcrumb(): ?BreadcrumbProperty
    {
        return $this->breadcrumb;
    }

    public function setBreadcrumb(?BreadcrumbProperty $breadcrumb): self
    {
        $this->breadcrumb = $breadcrumb;

        return $this;
    }
}

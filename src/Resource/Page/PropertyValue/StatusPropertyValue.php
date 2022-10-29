<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Page\PropertyValue;

use Brd6\NotionSdkPhp\Resource\Property\StatusProperty;

use function array_map;

class StatusPropertyValue extends AbstractPropertyValue
{
    protected ?StatusProperty $status = null;

    protected function initialize(): void
    {
        $data = (array)$this->getRawData()[$this->getType()];
        $this->status = StatusProperty::fromRawData($data);
    }

    /**
     * @return StatusProperty
     */
    public function getStatus(): array
    {
        return $this->status;
    }

    /**
     * @param StatusProperty $status
     */
    public function setStatus(array $status): self
    {
        $this->status = $status;

        return $this;
    }
}

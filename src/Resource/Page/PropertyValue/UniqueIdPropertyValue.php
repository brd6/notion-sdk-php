<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Page\PropertyValue;

class UniqueIdPropertyValue extends AbstractPropertyValue
{
    protected ?int $number = null;
    protected ?string $prefix = null;

    protected function initialize(): void
    {
        $data = (array) $this->getRawData()[$this->getType()];
        $this->number = (int) $data['number'];
        $this->prefix = isset($data['prefix']) ? (string) $data['prefix'] : null;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(?int $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getPrefix(): ?string
    {
        return $this->prefix;
    }

    public function setPrefix(?string $prefix): self
    {
        $this->prefix = $prefix;

        return $this;
    }
}

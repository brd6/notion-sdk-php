<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Filter;

class CompoundFilterProperty extends AbstractFilterProperty
{
    /**
     * @var AbstractFilterProperty[]|array
     */
    protected array $or;

    /**
     * @var AbstractFilterProperty[]|array
     */
    protected array $and;

    /**
     * @return array|AbstractFilterProperty[]
     */
    public function getOr(): array
    {
        return $this->or;
    }

    /**
     * @param array|AbstractFilterProperty[] $or
     */
    public function setOr(array $or): self
    {
        $this->or = $or;

        return $this;
    }

    /**
     * @return array|AbstractFilterProperty[]
     */
    public function getAnd(): array
    {
        return $this->and;
    }

    /**
     * @param array|AbstractFilterProperty[] $and
     */
    public function setAnd(array $and): self
    {
        $this->and = $and;

        return $this;
    }
}

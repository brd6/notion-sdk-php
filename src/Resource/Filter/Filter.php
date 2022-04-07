<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Filter;

class Filter
{
    /**
     * @var AbstractFilterProperty[]|array
     */
    protected array $properties = [];

    public function add(AbstractFilterProperty $filterProperty)
    {
        $this->properties[] = $filterProperty;
    }
}

<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Pagination;

use Brd6\NotionSdkPhp\Resource\User\AbstractUser;

use function array_map;

class UserResults extends AbstractPaginationResults
{
    protected function initialize(): void
    {
        $this->results = isset($this->getRawData()['results']) ? array_map(
            fn (array $resultRawData) => AbstractUser::fromRawData($resultRawData),
            (array) $this->getRawData()['results'],
        ) : [];
    }

    /**
     * @return AbstractUser[]|array
     */
    public function getResults(): array
    {
        return $this->results;
    }
}

<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Exception;

use Exception;

abstract class AbstractNotionException extends Exception
{
    abstract public function getMessageCode(): string;
}

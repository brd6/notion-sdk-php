<?php

declare(strict_types=1);

namespace Brd6\Test\NotionSdkPhp\Resource\Property;

use Brd6\NotionSdkPhp\Resource\Property\SyncedFromProperty;
use Brd6\Test\NotionSdkPhp\TestCase;
use ErrorException;

use function error_reporting;
use function restore_error_handler;
use function set_error_handler;

class SyncedFromPropertyTest extends TestCase
{
    public function testFromRawDataWithoutBlockIdDoesNotTriggerWarnings(): void
    {
        set_error_handler(static function (int $severity, string $message, string $file, int $line): bool {
            if ((error_reporting() & $severity) === 0) {
                return false;
            }

            throw new ErrorException($message, 0, $severity, $file, $line);
        });

        try {
            $property = SyncedFromProperty::fromRawData([
                'type' => 'block_id',
            ]);
        } finally {
            restore_error_handler();
        }

        $this->assertInstanceOf(SyncedFromProperty::class, $property);
        $this->assertSame('', $property->getBlockId());
    }
}

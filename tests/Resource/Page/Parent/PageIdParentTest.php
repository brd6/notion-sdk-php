<?php

declare(strict_types=1);

namespace Brd6\Test\NotionSdkPhp\Resource\Page\Parent;

use Brd6\NotionSdkPhp\Resource\Page\Parent\PageIdParent;
use Brd6\Test\NotionSdkPhp\TestCase;

class PageIdParentTest extends TestCase
{
    public function testToArray(): void
    {
        $pageId = 'c7b7a433-2345-4b57-93f8-8883a31e8529';

        $parent = (new PageIdParent())
            ->setPageId($pageId);

        $this->assertEquals(
            [
                'type' => 'page_id',
                'page_id' => $pageId,
            ],
            $parent->toArray(),
        );
    }
}

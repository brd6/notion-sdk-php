<?php

declare(strict_types=1);

namespace Brd6\Test\NotionSdkPhp\Resource\Page\PropertyValue;

use Brd6\NotionSdkPhp\Resource\Page;
use Brd6\NotionSdkPhp\Resource\Page\PropertyValue\VerificationPropertyValue;
use Brd6\NotionSdkPhp\Resource\Property\DateProperty;
use Brd6\NotionSdkPhp\Resource\User\AbstractUser;
use PHPUnit\Framework\TestCase;

use function file_get_contents;
use function json_decode;

class VerificationPropertyValueTest extends TestCase
{
    public function testUnverifiedProperty(): void
    {
        /** @var Page $page */
        $page = Page::fromRawData(
            (array) json_decode(
                (string) file_get_contents('tests/Fixtures/client_pages_retrieve_page_verification_unverified_200.json'),
                true,
            ),
        );

        $properties = $page->getProperties();
        /** @var VerificationPropertyValue $verification */
        $verification = $properties['Verification'];

        $this->assertEquals('verification', $verification->getType());
        $this->assertEquals('unverified', $verification->getState());
        $this->assertNull($verification->getVerifiedBy());
        $this->assertNull($verification->getDate());
    }

    public function testVerifiedProperty(): void
    {
        /** @var Page $page */
        $page = Page::fromRawData(
            (array) json_decode(
                (string) file_get_contents('tests/Fixtures/client_pages_retrieve_page_verification_verified_200.json'),
                true,
            ),
        );

        $properties = $page->getProperties();
        /** @var VerificationPropertyValue $verification */
        $verification = $properties['Verification'];

        $this->assertEquals('verification', $verification->getType());
        $this->assertEquals('verified', $verification->getState());

        $verifiedBy = $verification->getVerifiedBy();
        $this->assertInstanceOf(AbstractUser::class, $verifiedBy);
        $this->assertEquals('01e46064-d5fb-4444-8ecc-ad47d076f804', $verifiedBy->getId());
        $this->assertEquals('User Name', $verifiedBy->getName());

        $date = $verification->getDate();
        $this->assertInstanceOf(DateProperty::class, $date);
        $this->assertNotNull($date->getStart());
        $this->assertNotNull($date->getEnd());
    }
}

<?php

declare(strict_types=1);

namespace Brd6\Test\NotionSdkPhp\Resource;

use Brd6\NotionSdkPhp\Resource\Comment;
use Brd6\NotionSdkPhp\Resource\CommentAttachment;
use Brd6\NotionSdkPhp\Resource\File\File;
use Brd6\NotionSdkPhp\Resource\Page\Parent\PageIdParent;
use Brd6\NotionSdkPhp\Resource\RichText\Text;
use Brd6\NotionSdkPhp\Resource\User\PartialUser;
use Brd6\Test\NotionSdkPhp\TestCase;
use DateTimeImmutable;

use function file_get_contents;
use function json_decode;

class CommentTest extends TestCase
{
    public function testFromRawData(): void
    {
        $rawData = json_decode(
            (string) file_get_contents('tests/Fixtures/comment_200.json'),
            true,
        );

        $comment = Comment::fromRawData($rawData);

        $this->assertEquals('comment', $comment::getResourceType());
        $this->assertEquals('7a793800-3e55-4d5e-8009-2261de026179', $comment->getId());
        $this->assertEquals('f4be6752-a539-4da2-a8a9-c3953e13bc0b', $comment->getDiscussionId());

        $this->assertInstanceOf(DateTimeImmutable::class, $comment->getCreatedTime());
        $this->assertEquals('2022-07-15T21:17:00.000Z', $comment->getCreatedTime()->format('Y-m-d\TH:i:s.v\Z'));

        $this->assertInstanceOf(DateTimeImmutable::class, $comment->getLastEditedTime());
        $this->assertEquals('2022-07-15T21:17:00.000Z', $comment->getLastEditedTime()->format('Y-m-d\TH:i:s.v\Z'));

        $this->assertInstanceOf(PartialUser::class, $comment->getCreatedBy());
        $this->assertEquals('e450a39e-9051-4d36-bc4e-8581611fc592', $comment->getCreatedBy()->getId());

        $this->assertInstanceOf(PageIdParent::class, $comment->getParent());
        $this->assertEquals('5c6a2821-6bb1-4a7e-b6e1-c50111515c3d', $comment->getParent()->getPageId());

        $richText = $comment->getRichText();
        $this->assertCount(1, $richText);
        $this->assertInstanceOf(Text::class, $richText[0]);
        $this->assertEquals('Hello world', $richText[0]->getPlainText());

        $attachments = $comment->getAttachments();
        $this->assertCount(1, $attachments);
        $this->assertInstanceOf(CommentAttachment::class, $attachments[0]);
        $this->assertEquals('image', $attachments[0]->getCategory());
        $this->assertInstanceOf(File::class, $attachments[0]->getFile());
    }
}

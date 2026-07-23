<?php

declare(strict_types=1);

namespace Brd6\Test\NotionSdkPhp\Resource;

use Brd6\NotionSdkPhp\Resource\Block\ParagraphBlock;
use Brd6\NotionSdkPhp\Resource\Link;
use Brd6\NotionSdkPhp\Resource\Property\ParagraphProperty;
use Brd6\NotionSdkPhp\Resource\Property\TextProperty;
use Brd6\NotionSdkPhp\Resource\RichText\Text;
use PHPUnit\Framework\TestCase;

class LinkTest extends TestCase
{
    public function testItSerializesItsUrl(): void
    {
        $link = (new Link())->setUrl('https://example.test/a');

        $this->assertSame(['url' => 'https://example.test/a'], $link->toArray());
    }

    public function testItOmitsTheEmptyTypeNotionDoesNotExpect(): void
    {
        $link = (new Link())->setUrl('https://example.test/a');

        $this->assertArrayNotHasKey('type', $link->toArray());
    }

    /**
     * The API rejects the whole request when `text.link` arrives as an empty array:
     * "link should be an object, `null`, or `undefined`, instead was `[]`".
     */
    public function testALinkedTextSerializesAsAnObjectForCreate(): void
    {
        $text = Text::fromContent('Linked');
        $property = $text->getText();
        $this->assertInstanceOf(TextProperty::class, $property);
        $property->setLink((new Link())->setUrl('https://example.test/a'));

        $block = (new ParagraphBlock())->setParagraph((new ParagraphProperty())->setRichText([$text]));

        $data = $block->toArrayForCreate();

        $this->assertSame(
            ['url' => 'https://example.test/a'],
            $data['paragraph']['rich_text'][0]['text']['link'],
        );
    }

    public function testAnUnlinkedTextStillSerializesWithoutALink(): void
    {
        $block = (new ParagraphBlock())->setParagraph(
            (new ParagraphProperty())->setRichText([Text::fromContent('Plain')]),
        );

        $data = $block->toArrayForCreate();

        $this->assertArrayNotHasKey('link', $data['paragraph']['rich_text'][0]['text']);
    }

    public function testItKeepsRoundTrippingRawDataFromTheApi(): void
    {
        $property = TextProperty::fromRawData([
            'content' => 'Linked',
            'link' => ['url' => 'https://example.test/a'],
        ]);

        $link = $property->getLink();
        $this->assertInstanceOf(Link::class, $link);
        $this->assertSame('https://example.test/a', $link->getUrl());
    }
}

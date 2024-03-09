<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\RichText;

use Brd6\NotionSdkPhp\Exception\InvalidRichTextException;
use Brd6\NotionSdkPhp\Exception\UnsupportedRichTextTypeException;
use Brd6\NotionSdkPhp\Resource\AbstractJsonSerializable;
use Brd6\NotionSdkPhp\Resource\Annotations;
use Brd6\NotionSdkPhp\Util\StringHelper;

use function class_exists;

abstract class AbstractRichText extends AbstractJsonSerializable
{
    private array $rawData = [];
    protected string $plainText = '';
    protected ?string $href = null;
    protected string $type = '';
    protected ?Annotations $annotations = null;

    /**
     * @throws InvalidRichTextException
     * @throws UnsupportedRichTextTypeException
     */
    public static function fromRawData(array $rawData): self
    {
        if (!isset($rawData['type'])) {
            throw new InvalidRichTextException();
        }

        $class = static::getMapClassFromType((string) $rawData['type']);

        /** @var static $resource */
        $resource = new $class();

        $resource
            ->setRawData($rawData)
            ->initialize();

        return $resource;
    }

    protected function setRawData(array $rawData): self
    {
        $this->rawData = $rawData;

        $this->plainText = (string) ($this->rawData['plain_text'] ?? '');
        $this->href = isset($this->rawData['href']) ? (string) $this->rawData['href'] : null;
        $this->annotations = Annotations::fromRawData((array) $rawData['annotations']);
        $this->type = (string) ($this->rawData['type'] ?? '');

        return $this;
    }

    /**
     * @throws UnsupportedRichTextTypeException
     */
    protected static function getMapClassFromType(string $type): string
    {
        $typeFormatted = StringHelper::snakeCaseToCamelCase($type);
        $class = "Brd6\\NotionSdkPhp\\Resource\RichText\\$typeFormatted";

        if (!class_exists($class)) {
            throw new UnsupportedRichTextTypeException($type);
        }

        return $class;
    }

    abstract protected function initialize(): void;

    public function getRawData(): array
    {
        return $this->rawData;
    }

    public function getPlainText(): string
    {
        return $this->plainText;
    }

    /**
     * @param string $plainText
     *
     * @return AbstractRichText
     */
    public function setPlainText(string $plainText): self
    {
        $this->plainText = $plainText;

        return $this;
    }

    public function getHref(): ?string
    {
        return $this->href;
    }

    /**
     * @param string|null $href
     *
     * @return AbstractRichText
     */
    public function setHref(?string $href): self
    {
        $this->href = $href;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return AbstractRichText
     */
    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getAnnotations(): ?Annotations
    {
        return $this->annotations;
    }

    /**
     * @param Annotations|null $annotations
     *
     * @return AbstractRichText
     */
    public function setAnnotations(?Annotations $annotations): self
    {
        $this->annotations = $annotations;

        return $this;
    }
}

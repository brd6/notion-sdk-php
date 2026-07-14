<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Page;

class PageTemplate
{
    public const TYPE_NONE = 'none';
    public const TYPE_DEFAULT = 'default';
    public const TYPE_TEMPLATE_ID = 'template_id';

    protected string $type;
    protected ?string $templateId;
    protected ?string $timezone;

    private function __construct(string $type, ?string $templateId = null, ?string $timezone = null)
    {
        $this->type = $type;
        $this->templateId = $templateId;
        $this->timezone = $timezone;
    }

    /**
     * Creates the page without applying any template; only valid on page creation.
     */
    public static function none(): self
    {
        return new self(self::TYPE_NONE);
    }

    /**
     * Applies the data source's default template; the timezone resolves the template's
     * relative dates.
     */
    public static function defaultTemplate(?string $timezone = null): self
    {
        return new self(self::TYPE_DEFAULT, null, $timezone);
    }

    public static function templateId(string $templateId, ?string $timezone = null): self
    {
        return new self(self::TYPE_TEMPLATE_ID, $templateId, $timezone);
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getTemplateId(): ?string
    {
        return $this->templateId;
    }

    public function getTimezone(): ?string
    {
        return $this->timezone;
    }

    public function toArray(): array
    {
        $data = ['type' => $this->type];

        if ($this->templateId !== null) {
            $data['template_id'] = $this->templateId;
        }

        if ($this->timezone !== null) {
            $data['timezone'] = $this->timezone;
        }

        return $data;
    }
}

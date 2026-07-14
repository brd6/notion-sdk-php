<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\DataSource;

class DataSourceTemplate
{
    protected string $id = '';
    protected string $name = '';
    protected bool $isDefault = false;

    public static function fromRawData(array $rawData): self
    {
        $template = new self();

        $template->id = (string) ($rawData['id'] ?? '');
        $template->name = (string) ($rawData['name'] ?? '');
        $template->isDefault = (bool) ($rawData['is_default'] ?? false);

        return $template;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isDefault(): bool
    {
        return $this->isDefault;
    }
}

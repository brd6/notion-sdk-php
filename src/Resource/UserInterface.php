<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource;

interface UserInterface extends ResourceInterface
{
    public function getType(): ?string;

    public function getName(): ?string;

    public function getAvatarUrl(): ?string;
}

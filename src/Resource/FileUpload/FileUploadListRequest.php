<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\FileUpload;

use Brd6\NotionSdkPhp\Resource\AbstractJsonSerializable;

class FileUploadListRequest extends AbstractJsonSerializable
{
    protected ?string $status = null;

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }
}

<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Page\PropertyValue;

use Brd6\NotionSdkPhp\Exception\InvalidPropertyValueException;
use Brd6\NotionSdkPhp\Exception\UnsupportedPropertyValueException;

use function array_map;

class FilesPropertyValue extends AbstractPropertyValue
{
    /**
     * @var array|AbstractFilePropertyValue[]
     */
    protected array $files = [];

    /**
     * @throws InvalidPropertyValueException
     * @throws UnsupportedPropertyValueException
     */
    protected function initialize(): void
    {
        $data = (array) $this->getRawData()[$this->getType()];

        $this->files = array_map(
            fn (array $filesRawData) => AbstractFilePropertyValue::fromRawData($filesRawData),
            $data,
        );
    }

    /**
     * @return array|AbstractFilePropertyValue[]
     */
    public function getFiles(): array
    {
        return $this->files;
    }

    /**
     * @param array|AbstractFilePropertyValue[] $files
     */
    public function setFiles(array $files): self
    {
        $this->files = $files;

        return $this;
    }
}

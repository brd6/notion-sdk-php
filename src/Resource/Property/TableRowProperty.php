<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Resource\Property;

use Brd6\NotionSdkPhp\Exception\InvalidRichTextException;
use Brd6\NotionSdkPhp\Exception\UnsupportedRichTextTypeException;
use Brd6\NotionSdkPhp\Resource\RichText\AbstractRichText;

use function array_map;
use function count;

class TableRowProperty extends AbstractProperty
{
    /**
     * @var array<array<AbstractRichText>>
     */
    protected array $cells = [];

    /**
     * @param array $rawData
     *
     * @return TableRowProperty
     *
     * @throws InvalidRichTextException
     * @throws UnsupportedRichTextTypeException
     */
    public static function fromRawData(array $rawData): self
    {
        $property = new self();

        if (isset($rawData['cells'])) {
            /** @var array $cells */
            $cells = $rawData['cells'];
            $property->cells = self::createCellsFromRawData($cells);
        }

        return $property;
    }

    /**
     * @param array $cellsRawData
     *
     * @return array<array<AbstractRichText>>
     *
     * @throws InvalidRichTextException
     * @throws UnsupportedRichTextTypeException
     */
    private static function createCellsFromRawData(array $cellsRawData): array
    {
        $cells = [];

        /** @var array $cellData */
        foreach ($cellsRawData as $cellData) {
            $rawData = [];

            if (count($cellData) > 0) {
                /** @var array<AbstractRichText> $rawData */
                $rawData = array_map(static fn (array $data) => AbstractRichText::fromRawData($data), $cellData);
            }

            $cells[] = $rawData;
        }

        return $cells;
    }

    /**
     * @return array<array<AbstractRichText>>
     */
    public function getCells(): array
    {
        return $this->cells;
    }

    /**
     * @param array<array<AbstractRichText>> $cells
     */
    public function setCells(array $cells): self
    {
        $this->cells = $cells;

        return $this;
    }
}

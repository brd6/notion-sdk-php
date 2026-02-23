<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Endpoint;

use Brd6\NotionSdkPhp\Client;
use stdClass;

use function array_keys;
use function count;
use function is_array;

abstract class AbstractEndpoint
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    protected function getClient(): Client
    {
        return $this->client;
    }

    /**
     * @psalm-suppress MixedAssignment
     */
    protected static function normalizeEmptyPropertyConfigurations(array $data): array
    {
        if (!isset($data['properties']) || !is_array($data['properties'])) {
            return $data;
        }

        foreach ($data['properties'] as $propertyName => $propertyRawData) {
            if (!is_array($propertyRawData)) {
                continue;
            }

            $propertyConfigKeys = array_keys($propertyRawData);
            if (count($propertyConfigKeys) !== 1) {
                $data['properties'][$propertyName] = $propertyRawData;

                continue;
            }

            $propertyConfigKey = (string) $propertyConfigKeys[0];
            if (($propertyRawData[$propertyConfigKey] ?? null) === []) {
                $propertyRawData[$propertyConfigKey] = new stdClass();
            }

            $data['properties'][$propertyName] = $propertyRawData;
        }

        return $data;
    }
}

<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Endpoint;

use Brd6\NotionSdkPhp\Client;
use Brd6\NotionSdkPhp\ClientOptions;
use stdClass;

use function array_key_exists;
use function array_keys;
use function count;
use function is_array;
use function version_compare;

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

    protected function supportsVersion(string $minimumVersion): bool
    {
        return version_compare($this->getClient()->getOptions()->getNotionVersion(), $minimumVersion, '>=');
    }

    /**
     * Renames the `archived` key to `in_trash` for clients on Notion-Version 2026-03-11 or newer;
     * payloads for older versions are returned unchanged.
     *
     * @psalm-suppress MixedAssignment
     */
    protected function normalizeTrashKey(array $data): array
    {
        if (!$this->supportsVersion(ClientOptions::NOTION_VERSION_2026_03_11)) {
            return $data;
        }

        if (array_key_exists('archived', $data)) {
            $data['in_trash'] = $data['archived'];
            unset($data['archived']);
        }

        return $data;
    }

    /**
     * Object-valued nested config keys that Notion rejects when serialized as `[]` instead of `{}`.
     * Keyed by the parent config key; list-valued siblings such as `options` and `groups` are never touched.
     */
    private const NESTED_OBJECT_CONFIG_KEYS = [
        'relation' => ['single_property', 'dual_property'],
    ];

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

            if (count($propertyConfigKeys) === 1) {
                $propertyConfigKey = (string) $propertyConfigKeys[0];
            } elseif (isset($propertyRawData['type'])) {
                $propertyConfigKey = (string) $propertyRawData['type'];
            } else {
                $data['properties'][$propertyName] = $propertyRawData;

                continue;
            }

            $config = $propertyRawData[$propertyConfigKey] ?? null;

            if ($config === []) {
                $propertyRawData[$propertyConfigKey] = new stdClass();
            } elseif (is_array($config)) {
                $propertyRawData[$propertyConfigKey] = self::normalizeNestedEmptyConfig($propertyConfigKey, $config);
            }

            $data['properties'][$propertyName] = $propertyRawData;
        }

        return $data;
    }

    /**
     * @psalm-suppress MixedAssignment
     */
    private static function normalizeNestedEmptyConfig(string $configKey, array $config): array
    {
        foreach (self::NESTED_OBJECT_CONFIG_KEYS[$configKey] ?? [] as $nestedKey) {
            if (($config[$nestedKey] ?? null) === []) {
                $config[$nestedKey] = new stdClass();
            }
        }

        return $config;
    }
}

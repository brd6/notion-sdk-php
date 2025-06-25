<?php

declare(strict_types=1);

namespace NotionOAuth;

use League\OAuth2\Client\OptionProvider\HttpBasicAuthOptionProvider;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;

class NotionProvider extends AbstractProvider
{
    use BearerAuthorizationTrait;

    private const DEFAULT_OWNER = 'user';


    public function __construct(array $options = [])
    {
        $collaborators = [
            'optionProvider' => new HttpBasicAuthOptionProvider(),
        ];

        parent::__construct($options, $collaborators);
    }

    public function getBaseAuthorizationUrl(): string
    {
        return 'https://api.notion.com/v1/oauth/authorize';
    }

    public function getBaseAccessTokenUrl(array $params): string
    {
        return 'https://api.notion.com/v1/oauth/token';
    }

    public function getResourceOwnerDetailsUrl(AccessToken $token): string
    {
        return ''; // Notion doesn't provide route to get user resource
    }

    protected function getAuthorizationParameters(array $options): array
    {
        if (!array_key_exists('owner', $options)) {
            $options['owner'] = self::DEFAULT_OWNER;
        }

        $options = parent::getAuthorizationParameters($options);

        // Remove parameters that Notion doesn't use
        unset($options['approval_prompt']);
        unset($options['scope']);

        return $options;
    }

    protected function getDefaultScopes(): array
    {
        return [];
    }

    protected function checkResponse(ResponseInterface $response, $data): void
    {
        if (!array_key_exists('error', $data)) {
            return;
        }

        $code = 0;
        $error = $data['error'];

        if (is_array($error)) {
            $code = $error['code'];
            $error = $error['message'];
        }

        throw new IdentityProviderException($error, $code, $data);
    }

    protected function fetchResourceOwnerDetails(AccessToken $token)
    {
        return $token->getValues();
    }

    protected function createResourceOwner(array $response, AccessToken $token): NotionResourceOwner
    {
        return new NotionResourceOwner($response, $response['bot_id']);
    }
} 
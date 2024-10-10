<?php

namespace valtos\OAuthLogto\Providers;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use Psr\Http\Message\ResponseInterface;

class SS14Provider extends AbstractProvider
{
    public function getBaseAuthorizationUrl()
    {
        return 'https://account.spacestation14.com/connect/authorize';
    }

    public function getBaseAccessTokenUrl(array $params)
    {
        return 'https://account.spacestation14.com/connect/token';
    }

    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        return 'https://account.spacestation14.com/connect/userinfo';
    }

    protected function getDefaultScopes()
    {
        return ['openid', 'profile', 'email'];
    }

    protected function checkResponse(ResponseInterface $response, $data)
    {
        if (isset($data['error'])) {
            throw new IdentityProviderException($data['error_description'] ?? $data['error'], null, $data);
        }
    }

    protected function createResourceOwner(array $response, AccessToken $token)
    {
        return new SS14ResourceOwner($response);
    }

    protected function prepareAccessTokenResponse(array $result)
    {
        return [
            'access_token' => $result['access_token'],
            'id_token'     => $result['id_token'],
            'token_type'   => $result['token_type'],
            'expires_in'   => $result['expires_in'],
        ];
    }

    protected function getAuthorizationHeaders($token = null)
    {
        return ['Authorization' => 'Bearer ' . $token];
    }
}

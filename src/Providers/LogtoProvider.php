<?php

namespace Ssangyongsports\OAuthLogto\Providers;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use Psr\Http\Message\ResponseInterface;

class LogtoProvider extends AbstractProvider
{
    public function getBaseAuthorizationUrl()
    {
        return 'https://auth.ssangyongsports.eu.org/oidc/auth';
    }

    public function getBaseAccessTokenUrl(array $params)
    {
        return 'https://auth.ssangyongsports.eu.org/oidc/token';
    }

    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        return 'https://auth.ssangyongsports.eu.org/oidc/me';
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
        return new LogtoResourceOwner($response);
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

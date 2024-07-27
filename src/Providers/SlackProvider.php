<?php

/*
 * This file is part of blomstra/oauth-slack.
 *
 * Copyright (c) 2022 Team Blomstra.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Blomstra\OAuthSlack\Providers;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use Psr\Http\Message\ResponseInterface;

class SlackProvider extends AbstractProvider
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
        return ['openid', 'profile', 'email']; // OpenID Connect scopes
    }

    protected function checkResponse(ResponseInterface $response, $data)
    {
        if (isset($data['error'])) {
            throw new IdentityProviderException($data['error_description'] ?? $data['error'], null, $data);
        }
    }

    protected function createResourceOwner(array $response, AccessToken $token)
    {
        return new OpenIDResourceOwner($response);
    }

    protected function prepareAccessTokenResponse(array $result)
    {
        // OpenID Connect often uses id_token in addition to access_token
        return [
            'access_token' => $result['access_token'],
            'id_token'     => $result['id_token'], // Added for OpenID Connect
            'token_type'   => $result['token_type'],
            'expires_in'   => $result['expires_in'],
        ];
    }

    protected function getAuthorizationHeaders($token = null)
    {
        return ['Authorization' => 'Bearer ' . $token];
    }
}

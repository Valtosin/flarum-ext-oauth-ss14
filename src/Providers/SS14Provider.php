<?php

namespace valtos\OAuthSS14\Providers;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;

class SS14Provider extends AbstractProvider
{
    use BearerAuthorizationTrait;

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

    protected function getScopeSeparator()
    {
        return ' ';
    }

    protected function checkResponse(ResponseInterface $response, $data)
    {
        if (!empty($data['error'])) {
            $code = 0;
            $error = $data['error'];

            if (is_array($error)) {
                $code = $error['code'] ?? 0;
                $error = $error['message'] ?? 'Unknown error occurred';
            }

            throw new IdentityProviderException($error, $code, $data);
        }
    }

    protected function createResourceOwner(array $response, AccessToken $token)
    {
        return new SS14ResourceOwner($response);
    }

    protected function getAuthorizationHeaders($token = null)
    {
        return ['Authorization' => 'Bearer ' . $token];
    }

    protected function getAccessTokenOptions(array $params)
    {
        $options = parent::getAccessTokenOptions($params);

        if (isset($params['code_verifier'])) {
            $options['form_params']['code_verifier'] = $params['code_verifier'];
        }

        return $options;
    }

    public function getAccessToken($grant, array $options = [])
    {
        $token = parent::getAccessToken($grant, $options);

        // Store the ID token if it's present in the response
        if (isset($token->getValues()['id_token'])) {
            $token->setIdToken($token->getValues()['id_token']);
        }

        return $token;
    }
}

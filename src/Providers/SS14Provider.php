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

    protected $idToken;

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

    public function getAccessToken($grant, array $options = [])
    {
        $token = parent::getAccessToken($grant, $options);

        if (isset($token->getValues()['id_token'])) {
            $this->idToken = $token->getValues()['id_token'];
        }

        return $token;
    }

    public function getIdToken()
    {
        return $this->idToken;
    }
}

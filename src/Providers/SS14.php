<?php

namespace valtos\OAuthSS14\Providers;

use Flarum\Forum\Auth\Registration;
use FoF\OAuth\Provider;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;
use Flarum\Http\SessionAuthenticator;
use Illuminate\Support\Arr;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class SS14 extends Provider
{
    /**
     * @var SS14Provider
     */
    protected $provider;

    /**
     * @var SessionAuthenticator
     */
    protected $session;

    public function __construct(SessionAuthenticator $session)
    {
        $this->session = $session;
    }

    public function name(): string
    {
        return 'SS14';
    }

    public function link(): string
    {
        return 'https://account.spacestation14.com/connect';
    }

    public function fields(): array
    {
        return [
            'client_id'     => 'required',
            'client_secret' => 'required',
        ];
    }

    public function provider(string $redirectUri): AbstractProvider
    {
        return $this->provider = new SS14Provider([
            'clientId'     => $this->getSetting('client_id'),
            'clientSecret' => $this->getSetting('client_secret'),
            'redirectUri'  => $redirectUri,
        ]);
    }

    public function options(): array
    {
        return ['scope' => ['openid', 'email', 'profile']];
    }

    protected function getAuthorizationUrlOptions(): array
    {
        $codeVerifier = $this->generateCodeVerifier();
        $this->session->set('oauth2_code_verifier', $codeVerifier);

        return [
            'response_mode' => 'form_post',
            'response_type' => 'code',
            'prompt' => 'consent',
            'code_challenge' => $this->generateCodeChallenge($codeVerifier),
            'code_challenge_method' => 'S256',
        ];
    }

    protected function generateCodeVerifier(): string
    {
        $random = bin2hex(random_bytes(32));
        return rtrim(strtr(base64_encode($random), '+/', '-_'), '=');
    }

    protected function generateCodeChallenge(string $codeVerifier): string
    {
        $hash = hash('sha256', $codeVerifier, true);
        return rtrim(strtr(base64_encode($hash), '+/', '-_'), '=');
    }

    public function suggestions(Registration $registration, $user, string $token)
    {
        $this->verifyEmail($email = $user->getEmail());

        $registration
            ->provideTrustedEmail($email)
            ->provideAvatar($user->getImage())
            ->suggestUsername($user->getName())
            ->setPayload($user->toArray());
    }

    public function getAccessToken($grant, array $options = [])
    {
        if ($grant === 'authorization_code') {
            $codeVerifier = $this->session->get('oauth2_code_verifier');
            $this->session->remove('oauth2_code_verifier');

            if ($codeVerifier) {
                $options['code_verifier'] = $codeVerifier;
            }
        }

        return parent::getAccessToken($grant, $options);
    }

    public function getResourceOwner(AccessToken $token)
    {
        $response = $this->provider->getResourceOwner($token);

        if (method_exists($response, 'toArray')) {
            $userinfo = $response->toArray();
        } else {
            $userinfo = (array) $response;
        }

        // Validate the ID token if it's present
        if (isset($token->getValues()['id_token'])) {
            $this->validateIdToken($token->getValues()['id_token'], $userinfo['sub']);
        }

        return $response;
    }

    protected function validateIdToken(string $idToken, string $sub)
    {
        $keys = $this->getJwks(); // Метод для получения JWKS от провайдера
        $decoded = JWT::decode($idToken, $keys);

        // Проверка claims
        if ($decoded->iss !== 'https://account.spacestation14.com' ||
            $decoded->aud !== $this->getSetting('client_id') ||
            $decoded->sub !== $sub ||
            $decoded->exp < time()) {
            throw new \Exception('Invalid ID token');
        }
    }

    protected function getJwks()
    {
        $response = $this->getHttpClient()->get('https://account.spacestation14.com/.well-known/openid-configuration/jwks');
        $jwks = json_decode((string) $response->getBody(), true);

        $keys = [];
        foreach ($jwks['keys'] as $k) {
            $keys[$k['kid']] = new Key($k['n'], $k['e']);
        }

        return $keys;
    }
}

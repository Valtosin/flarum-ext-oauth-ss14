<?php

namespace valtos\OAuthLogto\Providers;

use Flarum\Forum\Auth\Registration;
use FoF\OAuth\Provider;
use League\OAuth2\Client\Provider\AbstractProvider;

class SS14 extends Provider
{
    /**
     * @var SS14Provider
     */
    protected $provider;

    public function name(): string
    {
        return 'logto';
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

    public function suggestions(Registration $registration, $user, string $token)
    {
        $this->verifyEmail($email = $user->getEmail());

        $registration
            ->provideTrustedEmail($email)
            ->provideAvatar($user->getImage())
            ->suggestUsername($user->getName())
            ->setPayload($user->toArray());
    }
}

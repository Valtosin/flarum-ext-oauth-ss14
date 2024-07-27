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

use Flarum\Forum\Auth\Registration;
use FoF\OAuth\Provider;
use League\OAuth2\Client\Provider\AbstractProvider;

class Slack extends Provider
{
    /**
     * @var OpenIDProvider
     */
    protected $provider;

    public function name(): string
    {
        return 'openid';
    }

    public function link(): string
    {
        return 'https://auth.ssangyongsports.eu.org/oidc'; // 更改為 OpenID Connect 的文檔鏈接
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
        return $this->provider = new OpenIDProvider([
            'clientId'     => $this->getSetting('client_id'),
            'clientSecret' => $this->getSetting('client_secret'),
            'redirectUri'  => $redirectUri,
        ]);
    }

    public function options(): array
    {
        return ['scope' => ['openid', 'email', 'profile']]; // 確保範圍符合 OpenID Connect 標準
    }

    public function suggestions(Registration $registration, $user, string $token)
    {
        $this->verifyEmail($email = $user->getEmail());

        $registration
            ->provideTrustedEmail($email)
            ->provideAvatar($user->getImage192()) // 確保用戶圖像符合 OpenID Connect 的要求
            ->suggestUsername($user->getName())
            ->setPayload($user->toArray());
    }
}

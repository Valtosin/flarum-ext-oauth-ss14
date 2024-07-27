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

use Illuminate\Support\Arr;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;

class SlackResourceOwner implements ResourceOwnerInterface
{
    protected $response;

    public function __construct(array $response)
    {
        $this->response = $response;
    }

    public function toArray()
    {
        return $this->response;
    }

    public function getId(): ?string
    {
        return Arr::get($this->response, 'id');
    }

    public function getName(): ?string
    {
        return Arr::get($this->response, 'name');
    }
    public function getImage192(): ?string
    {
        return Arr::get($this->response, 'avatar');
    }
}

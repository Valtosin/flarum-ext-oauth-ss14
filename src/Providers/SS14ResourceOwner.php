<?php

namespace valtos\OAuthSS14\Providers;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;

class SS14ResourceOwner implements ResourceOwnerInterface
{
    protected $response;

    public function __construct(array $response)
    {
        $this->response = $response;
    }

    public function getId()
    {
        return $this->response['sub'] ?? null;
    }

    public function getName()
    {
        return $this->response['name'] ?? null;
    }

    public function getEmail()
    {
        return $this->response['email'] ?? null;
    }

    public function getImage()
    {
        return $this->response['picture'] ?? null;
    }

    public function toArray()
    {
        return $this->response;
    }
}

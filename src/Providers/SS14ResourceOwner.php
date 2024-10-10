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

    public function getFamilyName()
    {
        return $this->response['family_name'] ?? null;
    }

    public function getGivenName()
    {
        return $this->response['given_name'] ?? null;
    }

    public function getMiddleName()
    {
        return $this->response['middle_name'] ?? null;
    }

    public function getNickname()
    {
        return $this->response['nickname'] ?? null;
    }

    public function getPreferredUsername()
    {
        return $this->response['preferred_username'] ?? null;
    }

    public function getProfile()
    {
        return $this->response['profile'] ?? null;
    }

    public function getWebsite()
    {
        return $this->response['website'] ?? null;
    }

    public function getGender()
    {
        return $this->response['gender'] ?? null;
    }

    public function getBirthdate()
    {
        return $this->response['birthdate'] ?? null;
    }

    public function getZoneInfo()
    {
        return $this->response['zoneinfo'] ?? null;
    }

    public function getLocale()
    {
        return $this->response['locale'] ?? null;
    }

    public function getUpdatedAt()
    {
        return $this->response['updated_at'] ?? null;
    }

    public function isEmailVerified()
    {
        return $this->response['email_verified'] ?? false;
    }

    public function toArray()
    {
        return $this->response;
    }
}

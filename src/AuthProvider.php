<?php

namespace StrmPrivacy\Driver;

use StrmPrivacy\Driver\Exceptions\AuthenticationException;

class AuthProvider
{
    protected const EXPIRATION_SLACK_SECONDS = 60;

    /** @var string $accessToken */
    protected $accessToken;

    /** @var string $refreshToken */
    protected $refreshToken;

    /** @var int $expiresAt */
    public $expiresAt;

    public function __construct(string $json)
    {
        $authData = json_decode($json);
        if (!isset($authData->access_token, $authData->refresh_token)) {
            throw new AuthenticationException('Invalid response from authenticate/refresh request');
        }

        $this->accessToken = (string)$authData->access_token;
        $this->refreshToken = (string)$authData->refresh_token;

        $this->expiresAt = (int)$authData->expires_in + time();
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    public function isExpired(): bool
    {
        return time() + self::EXPIRATION_SLACK_SECONDS >= $this->expiresAt;
    }
}

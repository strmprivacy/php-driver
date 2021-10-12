<?php

namespace Streammachine\Driver;

use Streammachine\Driver\Exceptions\AuthenticationException;

class AuthProvider
{
    protected const EXPIRATION_SLACK_SECONDS = 60;

    /** @var string $idToken */
    protected $idToken;

    /** @var string $refreshToken */
    protected $refreshToken;

    /** @var int $expiresAt */
    protected $expiresAt;

    public function __construct(string $json)
    {
        $authData = json_decode($json);

        if (!isset($authData->idToken, $authData->refreshToken, $authData->expiresAt)) {
            throw new AuthenticationException('Invalid response from authenticate/refresh request');
        }

        $this->idToken = (string)$authData->idToken;
        $this->refreshToken = (string)$authData->refreshToken;
        $this->expiresAt = (int)$authData->expiresAt;
    }

    public function getIdToken(): string
    {
        return $this->idToken;
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

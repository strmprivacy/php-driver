<?php

namespace Streammachine\Driver;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\RequestException;
use Streammachine\Driver\Exceptions\AuthenticationException;
use Streammachine\Driver\Exceptions\RefreshException;

class Client
{
    /** @var \Streammachine\Driver\AuthProvider $authProvider */
    protected $authProvider;

    /** @var \GuzzleHttp\Client $httpClient */
    protected $httpClient;

    /** @var string $billingId */
    protected $billingId;

    /** @var string $clientId */
    protected $clientId;

    /** @var string $clientSecret */
    protected $clientSecret;

    /** @var \Streammachine\Driver\Config $config */
    protected $config;

    public function __construct(
        string $billingId,
        string $clientId,
        string $clientSecret,
        array $customConfig = [],
        array $httpConfig = []
    ) {
        $this->billingId = $billingId;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;

        $this->config = new Config($customConfig);
        $this->httpClient = new HttpClient($httpConfig);
    }

    public function authenticate(): void
    {
        try {
            $response = $this->httpClient->request(
                'POST',
                $this->config->getAuthUri(),
                [
                    'json' => [
                        'billingId' => $this->billingId,
                        'clientId' => $this->clientId,
                        'clientSecret' => $this->clientSecret,
                    ],
                ]
            );
        } catch (RequestException $e) {
            throw new AuthenticationException(
                sprintf(
                    'Error authenticating to %s for billingId %s and clientId %s, status code: %d, message: %s',
                    $this->config->getAuthUri(),
                    $this->billingId,
                    $this->clientId,
                    $e->getCode(),
                    $e->getMessage(),
                )
            );
        }

        $this->authProvider = new AuthProvider($response->getBody()->getContents());
    }

    public function refresh(): void
    {
        if (!isset($this->authProvider)) {
            $this->authenticate();
            return;
        }

        try {
            $response = $this->httpClient->request(
                'POST',
                $this->config->getRefreshUri(),
                [
                    'json' => [
                        'refreshToken' => $this->authProvider->getRefreshToken(),
                    ],
                ]
            );
        } catch (RequestException $e) {
            throw new RefreshException(
                sprintf(
                    'Error refreshing auth token to %s for billingId %s and clientId %s, status code: %d, message: %s',
                    $this->config->getRefreshUri(),
                    $this->billingId,
                    $this->clientId,
                    $e->getCode(),
                    $e->getMessage(),
                )
            );
        }

        $this->authProvider = new AuthProvider($response->getBody()->getContents());
    }

    public function initAuthProvider(): void
    {
        if (!isset($this->authProvider)) {
            $this->authenticate();
            return;
        }

        if ($this->authProvider->isExpired()) {
            $this->refresh();
            return;
        }
    }

    public function authIsExpired(): bool
    {
        if (!isset($this->authProvider)) {
            return true;
        }

        return $this->authProvider->isExpired();
    }
}

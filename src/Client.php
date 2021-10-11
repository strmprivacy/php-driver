<?php

namespace Streammachine\Driver;

use GuzzleHttp\Client as HttpClient;

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
        // TODO try/catch http errors + throw custom exception
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

        $this->authProvider = new AuthProvider($response->getBody()->getContents());
    }

    public function refresh(): void
    {
        if (!isset($this->authProvider)) {
            $this->authenticate();
            return;
        }

        // TODO try/catch http errors + throw custom exception
        $response = $this->httpClient->request(
            'POST',
            $this->config->getRefreshUri(),
            [
                'json' => [
                    'refreshToken' => $this->authProvider->getRefreshToken(),
                ],
            ]
        );

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

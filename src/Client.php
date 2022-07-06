<?php

namespace StrmPrivacy\Driver;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\RequestException;
use PhpParser\Node\Scalar\String_;
use StrmPrivacy\Driver\Exceptions\AuthenticationException;
use StrmPrivacy\Driver\Exceptions\RefreshException;
use Curl\Curl;

class Client
{
    /** @var \StrmPrivacy\Driver\AuthProvider $authProvider */
    protected $authProvider;

    /** @var \GuzzleHttp\Client $httpClient */
    protected $httpClient;

    /** @var string $clientId */
    protected $clientId;

    /** @var string $clientSecret */
    protected $clientSecret;

    /** @var \StrmPrivacy\Driver\Config $config */
    protected $config;

    public function __construct(
        string $clientId,
        string $clientSecret,
        array  $customConfig = [],
        array  $httpConfig = []
    )
    {
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
                    'headers' => ['content-type' => 'application/x-www-form-urlencoded'],
                    'body' => sprintf("grant_type=client_credentials&client_id=%s&client_secret=%s", $this->clientId, $this->clientSecret)
                ]
            );
        } catch (RequestException $e) {
            throw new AuthenticationException(
                sprintf(
                    'Error authenticating to %s for clientId %s, status code: %d, message: %s',
                    $this->config->getAuthUri(),
                    $this->clientId,
                    $e->getCode(),
                    $e->getMessage()
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
                $this->config->getAuthUri(),
                [
                    'headers' => ['content-type' => 'application/x-www-form-urlencoded'],
                    'body' => sprintf("grant_type=refresh_token&refresh_token=%s", $this->authProvider->getRefreshToken())
                ]
            );
        } catch (RequestException $e) {
            throw new RefreshException(
                sprintf(
                    'Error refreshing auth token to %s for clientId %s, status code: %d, message: %s',
                    $this->config->getRefreshUri(),
                    $this->clientId,
                    $e->getCode(),
                    $e->getMessage()
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

    public function getAccessToken(): string
    {
        return $this->authProvider->getAccessToken();
    }
}

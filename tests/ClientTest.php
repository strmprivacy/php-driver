<?php

namespace Tests;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use StrmPrivacy\Driver\Client;
use StrmPrivacy\Driver\Config;

class ClientTest extends TestCase
{
    public function testClientCanBeInstantiated(): void
    {
        $this->assertInstanceOf(
            Client::class,
            new Client('clientId', 'clientSecret')
        );
    }

    public function testClientCanAuthenticateWithKeycloak(): void
    {
        static::markTestSkipped('When enabling this test, enter client credentials');

        $config = new Config(['keycloakHost' => 'accounts.dev.strmprivacy.io']);
        $client = new Client('clientId', 'clientSecret', (array)$config);
        $client->authenticate();
        $this->assertNotTrue($client->getAccessToken() == '', 'Access Token is empty');
    }

    public function testClientCanRefreshWithKeycloak(): void
    {
        static::markTestSkipped('When enabling this test, enter client credentials');

        $config = new Config(['keycloakHost' => 'accounts.dev.strmprivacy.io']);
        $client = new Client('clientId', 'clientSecret', (array)$config);
        $client->authenticate();
        $oldToken = $client->getAccessToken();
        $client->refresh();
        $this->assertTrue($client->getAccessToken() != '', 'Access Token is empty');
        $this->assertTrue($client->getAccessToken() != $oldToken, 'Access Token has not changed');
    }


    public function testClientCanAuthenticate(): void
    {
        $validResponse = $this->getMockResponse(61);
        $mockHandler = new MockHandler([$validResponse]);
        $handlerStack = HandlerStack::create($mockHandler);
        $client = new Client('clientId', 'clientSecret', [], ['handler' => $handlerStack]);

        $client->authenticate();
        $this->assertTrue(!$client->authIsExpired());
    }

    public function testClientShouldBeExpiredOnTime(): void
    {
        $almostExpiredResponse = $this->getMockResponse(59);
        $mockHandler = new MockHandler([$almostExpiredResponse]);
        $handlerStack = HandlerStack::create($mockHandler);
        $client = new Client('clientId', 'clientSecret', [], ['handler' => $handlerStack]);

        $client->authenticate();
        $this->assertTrue($client->authIsExpired());
    }

    public function testExpiredAuthShouldRefresh(): void
    {
        $expiredResponse = $this->getMockResponse();
        $validResponse = $this->getMockResponse(100);
        $mockHandler = new MockHandler([$expiredResponse, $validResponse]);
        $handlerStack = HandlerStack::create($mockHandler);
        $client = new Client('clientId', 'clientSecret', [], ['handler' => $handlerStack]);

        $client->authenticate();
        $client->refresh();
        $this->assertTrue(!$client->authIsExpired());
    }

    protected function getMockResponse(int $expiresIn = null): Response
    {
        return new Response(
            200,
            [],
            json_encode([
                'access_token' => 'dummy idToken',
                'refresh_token' => 'dummy refreshToken',
                'expires_in' => $expiresIn,
            ])
        );
    }
}

<?php

namespace Tests;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Streammachine\Driver\Client;

class ClientTest extends TestCase
{
    public function testClientCanBeInstantiated(): void
    {
        $this->assertInstanceOf(
            Client::class,
            new Client('billingId', 'clientId', 'clientSecret')
        );
    }

    public function testClientCanAuthenticate(): void
    {
        $validResponse = $this->getMockResponse();
        $mockHandler = new MockHandler([$validResponse]);
        $handlerStack = HandlerStack::create($mockHandler);
        $client = new Client('billingId', 'clientId', 'clientSecret', [], ['handler' => $handlerStack]);

        $client->authenticate();
        $this->assertTrue(!$client->authIsExpired());
    }

    public function testClientShouldBeExpiredOnTime(): void
    {
        $almostExpiredResponse = $this->getMockResponse(time() + 59);
        $mockHandler = new MockHandler([$almostExpiredResponse]);
        $handlerStack = HandlerStack::create($mockHandler);
        $client = new Client('billingId', 'clientId', 'clientSecret', [], ['handler' => $handlerStack]);

        $client->authenticate();
        $this->assertTrue($client->authIsExpired());
    }

    public function testExpiredAuthShouldRefresh(): void
    {
        $expiredResponse = $this->getMockResponse(time() - 100);
        $validResponse = $this->getMockResponse();
        $mockHandler = new MockHandler([$expiredResponse, $validResponse]);
        $handlerStack = HandlerStack::create($mockHandler);
        $client = new Client('billingId', 'clientId', 'clientSecret', [], ['handler' => $handlerStack]);

        $client->authenticate();
        $client->refresh();
        $this->assertTrue(!$client->authIsExpired());
    }

    protected function getMockResponse(int $expiresAt = null): Response
    {
        $expiresAt = is_null($expiresAt) ? time() + 3600 : $expiresAt;

        return new Response(
            200,
            [],
            json_encode([
                'idToken' => 'dummy idToken',
                'refreshToken' => 'dummy refreshToken',
                'expiresAt' => $expiresAt,
            ])
        );
    }
}

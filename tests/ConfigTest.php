<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Streammachine\Driver\Config;

class ConfigTest extends TestCase
{
    public function testConfigCanBeInstantiated(): void
    {
        $this->assertInstanceOf(Config::class, new Config());
    }

    public function testCustomGatewayUriIsCorrect(): void
    {
        $config = new Config([
            'gatewayHost' => 'customhost',
            'gatewayEndpoint' => 'customendpoint',
        ]);

        $this->assertEquals(
            'https://customhost/customendpoint',
            $config->getGatewayUri()
        );
    }

    public function testCustomAuthUriIsCorrect(): void
    {
        $config = new Config([
            'stsHost' => 'customhost',
            'stsAuthEndpoint' => 'customendpoint',
        ]);

        $this->assertEquals(
            'https://customhost/customendpoint',
            $config->getAuthUri()
        );
    }

    public function testCustomRefreshUriIsCorrect(): void
    {
        $config = new Config([
            'stsHost' => 'customhost',
            'stsRefreshEndpoint' => 'customendpoint',
        ]);

        $this->assertEquals(
            'https://customhost/customendpoint',
            $config->getRefreshUri()
        );
    }
}

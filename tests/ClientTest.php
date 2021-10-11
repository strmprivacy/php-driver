<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Streammachine\Driver\Client;

class ClientTest extends TestCase
{
    public function testClientCanBeInstantiated(): void
    {
        $this->assertInstanceOf(Client::class, new Client());
    }
}

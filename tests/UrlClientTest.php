<?php

namespace Permafrost\RayCli\Tests;

use Permafrost\RayCli\UrlClient;
use PHPUnit\Framework\TestCase;

class UrlClientTest extends TestCase
{
    /** @test */
    public function it_retrieves_urls(): void
    {
        $methods = [
            'get', 'delete', 'head', 'options', 'patch', 'post', 'put',
        ];

        $client = new UrlClient();

        foreach ($methods as $method => $timeoutMs) {
            $client->timeoutMs = 150;

            $result = $client->retrieve($method, 'http://localhost:23517/');

            $this->assertNotNull($result);
        }
    }
}

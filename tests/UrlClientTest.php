<?php

namespace Permafrost\RayCli\Tests;

use Permafrost\RayCli\UrlClient;
use PHPUnit\Framework\TestCase;

class UrlClientTest extends TestCase
{
    /** @test */
    public function it_retrieves_urls()
    {
        $methods = [
            'get', 'delete', 'patch', 'post', 'put',
        ];

        $client = new UrlClient();

        foreach ($methods as $method) {
            $result = $client->retrieve($method, 'https://static.permafrost.dev/test-data.json');
            $this->assertNotNull($result);
        }
    }
}

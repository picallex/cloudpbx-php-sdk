<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Cloudpbx\Protocol\ProtocolHTTP;
use Cloudpbx\Sdk\Implementation\Client;
use Cloudpbx\Protocol\Http\Request;
use Cloudpbx\Protocol\Http\Response;
use Cloudpbx\Protocol\Http\Implementation\ResponseFromArray;

class GatewayTest extends TestCase
{
    /**
     * Transport que rutea la respuesta segun el path pedido, porque all()
     * hace el walk endpoints -> gateways (varias GET).
     *
     * @param array<string, array<mixed>> $routes  path => decoded "data"
     */
    private function routingTransport(array $routes)
    {
        return new class ($routes) implements \Cloudpbx\Protocol\Http\Client {
            /** @var array<string> */
            public $urls = [];
            /** @var array<string, array<mixed>> */
            private $routes;

            public function __construct($routes)
            {
                $this->routes = $routes;
            }

            public function sendRequest(Request $request): Response
            {
                $url = $request->url();
                $this->urls[] = $url;

                foreach ($this->routes as $path => $data) {
                    if (substr($url, -strlen($path)) === $path) {
                        return new ResponseFromArray(json_encode(['data' => $data]), 200);
                    }
                }

                return new ResponseFromArray(json_encode(['data' => []]), 200);
            }
        };
    }

    private function clientWith($transport)
    {
        return new Client(new ProtocolHTTP('https://api.example.com', 'KEY', $transport));
    }

    public function testAllWalksEndpointsAndFlattensCatalog(): void
    {
        $transport = $this->routingTransport([
            '/api/v1/management/endpoints' => [
                ['id' => 1, 'name' => 'ep-a'],
                ['id' => 2, 'name' => 'ep-b'],
            ],
            '/api/v1/root/endpoints/1/gateways' => [
                ['id' => 5, 'name' => 'telinta', 'realm' => 'sip.vip2phone.net'],
            ],
            '/api/v1/root/endpoints/2/gateways' => [
                ['id' => 6, 'name' => 'commio', 'realm' => 'sip.commio.net'],
                ['id' => 7, 'name' => 'bandwidth', 'realm' => 'sip.bandwidth.com'],
            ],
        ]);
        $client = $this->clientWith($transport);

        $gateways = $client->gateways->all();

        $this->assertCount(3, $gateways);
        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\Gateway::class, $gateways[0]);

        $this->assertEquals(5, $gateways[0]->id);
        $this->assertEquals('telinta', $gateways[0]->name);
        $this->assertEquals('sip.vip2phone.net', $gateways[0]->realm);
        $this->assertEquals(1, $gateways[0]->endpoint_id);

        $this->assertEquals(6, $gateways[1]->id);
        $this->assertEquals(2, $gateways[1]->endpoint_id);
        $this->assertEquals(7, $gateways[2]->id);
        $this->assertEquals(2, $gateways[2]->endpoint_id);

        $this->assertContains('https://api.example.com/api/v1/management/endpoints', $transport->urls);
        $this->assertContains('https://api.example.com/api/v1/root/endpoints/1/gateways', $transport->urls);
        $this->assertContains('https://api.example.com/api/v1/root/endpoints/2/gateways', $transport->urls);
    }

    public function testAllIsEmptyWhenNoEndpoints(): void
    {
        $transport = $this->routingTransport(['/api/v1/management/endpoints' => []]);
        $client = $this->clientWith($transport);

        $this->assertEquals([], $client->gateways->all());
    }
}

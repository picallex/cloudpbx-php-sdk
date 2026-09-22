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

class MaintenanceTest extends TestCase
{
    /**
     * Transport that returns a verbatim JSON body per path. Unlike the
     * management transport, it does NOT wrap in a `data` envelope, because the
     * maintenance endpoints answer with a bare body.
     *
     * @param array<string, mixed> $routes  path => decoded body
     */
    private function rawTransport(array $routes)
    {
        return new class ($routes) implements \Cloudpbx\Protocol\Http\Client {
            /** @var array<string, mixed> */
            private $routes;

            public function __construct($routes)
            {
                $this->routes = $routes;
            }

            public function sendRequest(Request $request): Response
            {
                $url = $request->url();

                foreach ($this->routes as $path => $body) {
                    if (substr($url, -strlen($path)) === $path) {
                        return new ResponseFromArray(json_encode($body), 200);
                    }
                }

                return new ResponseFromArray(json_encode([]), 200);
            }
        };
    }

    private function clientWith($transport)
    {
        return new Client(new ProtocolHTTP('https://api.example.com', 'KEY', $transport));
    }

    public function testFreeswitchStatusReturnsBareArrayVerbatim(): void
    {
        $body = [
            ['name' => 'cloudpbx-agent@10.0.0.1', 'node' => ['alive' => true], 'sofia_status' => "raw\ttext"],
        ];
        $client = $this->clientWith($this->rawTransport([
            '/api/v1/maintenance/freeswitch/status' => $body,
        ]));

        // Bare array must pass through untouched (no `data` unwrap).
        $this->assertSame($body, $client->maintenance->freeswitchStatus());
    }

    public function testFreeswitchStatusUnwrapsDataEnvelope(): void
    {
        // apidev wraps this route in {data: [...]}, other maintenance routes do not
        $nodes = [['name' => 'cloudpbx-agent@10.0.0.1', 'node' => ['alive' => true]]];
        $client = $this->clientWith($this->rawTransport([
            '/api/v1/maintenance/freeswitch/status' => ['data' => $nodes],
        ]));

        $this->assertSame($nodes, $client->maintenance->freeswitchStatus());
    }

    public function testFreeswitchRegistrationsReturnsNodeKeyedMap(): void
    {
        $body = ['cloudpbx-agent@10.0.0.1' => []];
        $client = $this->clientWith($this->rawTransport([
            '/api/v1/maintenance/freeswitch/status/registrations' => $body,
        ]));

        $this->assertSame($body, $client->maintenance->freeswitchRegistrations());
    }
}

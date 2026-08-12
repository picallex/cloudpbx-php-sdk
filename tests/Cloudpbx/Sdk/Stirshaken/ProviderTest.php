<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.
//
// @author (2026) Agustin Serra <agustin@picallex.com>

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Cloudpbx\Protocol\ProtocolHTTP;
use Cloudpbx\Sdk\Stirshaken\Implementation\Client;
use Cloudpbx\Protocol\Http\Request;
use Cloudpbx\Protocol\Http\Response;
use Cloudpbx\Protocol\Http\Implementation\ResponseFromArray;

class StirshakenProviderTest extends TestCase
{
    private function fakeTransport($body, $status_code = 200)
    {
        return new class ($body, $status_code) implements \Cloudpbx\Protocol\Http\Client {
            /** @var string */
            public $last_url;
            /** @var string */
            public $last_method;
            /** @var string|null */
            public $last_body;
            /** @var string */
            private $body;
            /** @var int */
            private $status_code;

            public function __construct($body, $status_code)
            {
                $this->body = $body;
                $this->status_code = $status_code;
            }

            public function sendRequest(Request $request): Response
            {
                $this->last_url = $request->url();
                $this->last_method = $request->method();
                $this->last_body = $request->body();
                return new ResponseFromArray($this->body, $this->status_code);
            }
        };
    }

    private function clientWith($transport)
    {
        return new Client(new ProtocolHTTP('https://stir.example', 'KEY', $transport));
    }

    private function providerPayload($over = [])
    {
        return array_merge([
            'id' => 'prov-1',
            'tenant_id' => 't1',
            'name' => 'Telnyx',
            'priority' => 1,
            'weight' => 100,
            'destination_uri' => 'sip:telnyx',
            'cps_limit' => 10,
            'provider_prefix' => null,
            'is_active' => true,
            'created_at' => '2026-08-11T00:00:00',
            'ips' => [],
        ], $over);
    }

    public function testAllMapsBareArray(): void
    {
        $transport = $this->fakeTransport(json_encode([$this->providerPayload()]));
        $client = $this->clientWith($transport);

        $providers = $client->providers->all();

        $this->assertEquals('GET', $transport->last_method);
        $this->assertEquals('https://stir.example/api/v1/providers', $transport->last_url);
        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\Stirshaken\Provider::class, $providers[0]);
        $this->assertEquals('Telnyx', $providers[0]->name);
    }

    public function testCreateSendsBody(): void
    {
        $transport = $this->fakeTransport(json_encode($this->providerPayload()));
        $client = $this->clientWith($transport);

        $p = $client->providers->create(['name' => 'Telnyx', 'destination_uri' => 'sip:telnyx']);

        $this->assertEquals('POST', $transport->last_method);
        $this->assertEquals('https://stir.example/api/v1/providers', $transport->last_url);
        $this->assertEquals('Telnyx', json_decode($transport->last_body, true)['name']);
        $this->assertEquals('prov-1', $p->id);
    }

    public function testUpdateUsesPut(): void
    {
        $transport = $this->fakeTransport(json_encode($this->providerPayload(['weight' => 200])));
        $client = $this->clientWith($transport);

        $p = $client->providers->update('prov-1', ['weight' => 200]);

        $this->assertEquals('PUT', $transport->last_method);
        $this->assertEquals('https://stir.example/api/v1/providers/prov-1', $transport->last_url);
        $this->assertEquals(200, $p->weight);
    }

    public function testDeleteUsesDelete(): void
    {
        $transport = $this->fakeTransport('', 204);
        $client = $this->clientWith($transport);

        $client->providers->delete('prov-1');

        $this->assertEquals('DELETE', $transport->last_method);
        $this->assertEquals('https://stir.example/api/v1/providers/prov-1', $transport->last_url);
    }

    public function testIpsListsProviderIps(): void
    {
        $transport = $this->fakeTransport(json_encode([
            ['id' => 'pip-1', 'provider_id' => 'prov-1', 'ip_address' => '5.6.7.8', 'port' => 5060, 'priority' => 1, 'is_active' => true, 'created_at' => '2026-08-11T00:00:00'],
        ]));
        $client = $this->clientWith($transport);

        $ips = $client->providers->ips('prov-1');

        $this->assertEquals('GET', $transport->last_method);
        $this->assertEquals('https://stir.example/api/v1/providers/prov-1/ips', $transport->last_url);
        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\Stirshaken\ProviderIp::class, $ips[0]);
        $this->assertEquals('5.6.7.8', $ips[0]->ip_address);
    }

    public function testAddIpSendsBody(): void
    {
        $transport = $this->fakeTransport(json_encode(
            ['id' => 'pip-1', 'provider_id' => 'prov-1', 'ip_address' => '5.6.7.8', 'port' => 5060, 'priority' => 1, 'is_active' => true, 'created_at' => '2026-08-11T00:00:00']
        ));
        $client = $this->clientWith($transport);

        $pip = $client->providers->addIp('prov-1', ['ip_address' => '5.6.7.8']);

        $this->assertEquals('POST', $transport->last_method);
        $this->assertEquals('https://stir.example/api/v1/providers/prov-1/ips', $transport->last_url);
        $this->assertEquals('5.6.7.8', json_decode($transport->last_body, true)['ip_address']);
        $this->assertEquals('pip-1', $pip->id);
    }

    public function testRemoveIpUsesDelete(): void
    {
        $transport = $this->fakeTransport('', 204);
        $client = $this->clientWith($transport);

        $client->providers->removeIp('prov-1', 'pip-1');

        $this->assertEquals('DELETE', $transport->last_method);
        $this->assertEquals('https://stir.example/api/v1/providers/prov-1/ips/pip-1', $transport->last_url);
    }
}

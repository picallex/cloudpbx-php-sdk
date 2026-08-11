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

class StirshakenIpTest extends TestCase
{
    /**
     * fake transport that records the last request and returns a canned body.
     *
     * @param string $body
     * @param int $status_code
     * @return object
     */
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

    /**
     * @param object $transport
     * @return Client
     */
    private function clientWith($transport)
    {
        return new Client(new ProtocolHTTP('https://api.example.com', 'KEY', $transport));
    }

    /**
     * respuesta real del backend: array plano, sin envelope "data".
     *
     * @return array<string, mixed>
     */
    private function ipPayload()
    {
        return [
            'id' => 'daa2a03f-0f8c-4d80-8d38-b75b338a4584',
            'ip_cidr' => '203.0.113.10/32',
            'description' => 'prueba inbound',
            'certificate_id' => null,
            'certificate_name' => null,
            'forwarding_mode' => 'redirect',
            'destination_uri' => null,
            'call_direction' => 'inbound',
            'cps_limit' => 10,
            'customer_prefix' => null,
            'provider_prefix' => null,
            'is_active' => true,
            'created_at' => '2026-08-11T15:23:53.122721',
        ];
    }

    public function testAllBuildsQueryAndMapsBareArray(): void
    {
        $transport = $this->fakeTransport(json_encode([$this->ipPayload()]));
        $client = $this->clientWith($transport);

        $ips = $client->ips->all();

        $this->assertEquals('GET', $transport->last_method);
        $this->assertEquals('https://api.example.com/api/v1/ips', $transport->last_url);
        $this->assertCount(1, $ips);
        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\Stirshaken\Ip::class, $ips[0]);
        $this->assertEquals('203.0.113.10/32', $ips[0]->ip_cidr);
        $this->assertEquals('inbound', $ips[0]->call_direction);
        $this->assertTrue($ips[0]->is_active);
    }

    public function testCreateSendsBodyAndMapsBareObject(): void
    {
        $transport = $this->fakeTransport(json_encode($this->ipPayload()));
        $client = $this->clientWith($transport);

        $ip = $client->ips->create([
            'ip_cidr' => '203.0.113.10',
            'call_direction' => 'inbound',
            'forwarding_mode' => 'redirect',
        ]);

        $this->assertEquals('POST', $transport->last_method);
        $this->assertEquals('https://api.example.com/api/v1/ips', $transport->last_url);
        $this->assertEquals('203.0.113.10', json_decode($transport->last_body, true)['ip_cidr']);
        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\Stirshaken\Ip::class, $ip);
        $this->assertEquals('daa2a03f-0f8c-4d80-8d38-b75b338a4584', $ip->id);
    }

    public function testUpdateUsesPut(): void
    {
        $transport = $this->fakeTransport(json_encode($this->ipPayload()));
        $client = $this->clientWith($transport);

        $client->ips->update('daa2a03f-0f8c-4d80-8d38-b75b338a4584', ['description' => 'EDITADA']);

        $this->assertEquals('PUT', $transport->last_method);
        $this->assertEquals(
            'https://api.example.com/api/v1/ips/daa2a03f-0f8c-4d80-8d38-b75b338a4584',
            $transport->last_url
        );
    }

    public function testDeleteUsesDelete(): void
    {
        $transport = $this->fakeTransport('', 204);
        $client = $this->clientWith($transport);

        $client->ips->delete('daa2a03f-0f8c-4d80-8d38-b75b338a4584');

        $this->assertEquals('DELETE', $transport->last_method);
        $this->assertEquals(
            'https://api.example.com/api/v1/ips/daa2a03f-0f8c-4d80-8d38-b75b338a4584',
            $transport->last_url
        );
    }

    public function testUpdateProviderUsesPatch(): void
    {
        $assignment = [
            'id' => 'a1',
            'ip_id' => 'daa2a03f-0f8c-4d80-8d38-b75b338a4584',
            'provider_id' => 'p1',
            'provider_name' => 'Carrier X',
            'weight' => 200,
            'created_at' => '2026-08-11T15:23:53.122721',
        ];
        $transport = $this->fakeTransport(json_encode($assignment));
        $client = $this->clientWith($transport);

        $out = $client->ips->updateProvider(
            'daa2a03f-0f8c-4d80-8d38-b75b338a4584',
            'a1',
            ['weight' => 200]
        );

        $this->assertEquals('PATCH', $transport->last_method);
        $this->assertEquals(
            'https://api.example.com/api/v1/ips/daa2a03f-0f8c-4d80-8d38-b75b338a4584/providers/a1',
            $transport->last_url
        );
        $this->assertEquals(200, $out->weight);
    }

    public function testCreateRejectsNonArrayParams(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $transport = $this->fakeTransport(json_encode($this->ipPayload()));
        $client = $this->clientWith($transport);

        /** @phpstan-ignore-next-line intentional wrong type */
        $client->ips->create('not-an-array');
    }
}

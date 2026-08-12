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

class StirshakenCertificateTest extends TestCase
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

    private function certPayload()
    {
        return [
            'id' => 'cert-1',
            'name' => 'primary',
            'x5u_url' => 'https://x5u.example/cert.pem',
            'attest_level' => 'A',
            'is_primary' => true,
            'is_active' => true,
            'not_before' => null,
            'not_after' => '2027-01-01T00:00:00',
            'created_at' => '2026-08-11T00:00:00',
        ];
    }

    public function testAllMapsBareArray(): void
    {
        $transport = $this->fakeTransport(json_encode([$this->certPayload()]));
        $client = $this->clientWith($transport);

        $certs = $client->certificates->all();

        $this->assertEquals('GET', $transport->last_method);
        $this->assertEquals('https://stir.example/api/v1/certificates', $transport->last_url);
        $this->assertCount(1, $certs);
        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\Stirshaken\Certificate::class, $certs[0]);
        $this->assertEquals('primary', $certs[0]->name);
        $this->assertEquals('A', $certs[0]->attest_level);
        $this->assertTrue($certs[0]->is_primary);
    }

    public function testCreateSendsBody(): void
    {
        $transport = $this->fakeTransport(json_encode($this->certPayload()));
        $client = $this->clientWith($transport);

        $cert = $client->certificates->create(['name' => 'primary', 'attest_level' => 'A']);

        $this->assertEquals('POST', $transport->last_method);
        $this->assertEquals('https://stir.example/api/v1/certificates', $transport->last_url);
        $this->assertEquals('primary', json_decode($transport->last_body, true)['name']);
        $this->assertEquals('cert-1', $cert->id);
    }

    public function testDeleteUsesDelete(): void
    {
        $transport = $this->fakeTransport('', 204);
        $client = $this->clientWith($transport);

        $client->certificates->delete('cert-1');

        $this->assertEquals('DELETE', $transport->last_method);
        $this->assertEquals('https://stir.example/api/v1/certificates/cert-1', $transport->last_url);
    }
}

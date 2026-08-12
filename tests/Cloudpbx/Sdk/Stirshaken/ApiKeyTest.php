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

class StirshakenApiKeyTest extends TestCase
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

    public function testAllMapsBareArray(): void
    {
        $transport = $this->fakeTransport(json_encode([
            ['id' => 'k1', 'name' => 'postman', 'key_preview' => 'sk_abc…', 'last_used_at' => null, 'created_at' => '2026-08-11T00:00:00'],
        ]));
        $client = $this->clientWith($transport);

        $keys = $client->apiKeys->all();

        $this->assertEquals('GET', $transport->last_method);
        $this->assertEquals('https://stir.example/api/v1/api-keys', $transport->last_url);
        $this->assertCount(1, $keys);
        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\Stirshaken\ApiKey::class, $keys[0]);
        $this->assertEquals('postman', $keys[0]->name);
    }

    public function testCreateSendsNameAndReturnsRawKey(): void
    {
        $transport = $this->fakeTransport(json_encode([
            'id' => 'k1', 'name' => 'postman', 'key_preview' => 'sk_abc…',
            'raw_key' => 'sk_full_secret', 'last_used_at' => null, 'created_at' => '2026-08-11T00:00:00',
        ]));
        $client = $this->clientWith($transport);

        $key = $client->apiKeys->create('postman');

        $this->assertEquals('POST', $transport->last_method);
        $this->assertEquals('https://stir.example/api/v1/api-keys', $transport->last_url);
        $this->assertEquals('postman', json_decode($transport->last_body, true)['name']);
        $this->assertEquals('sk_full_secret', $key->raw_key);
    }

    public function testDeleteUsesDelete(): void
    {
        $transport = $this->fakeTransport('', 204);
        $client = $this->clientWith($transport);

        $client->apiKeys->delete('k1');

        $this->assertEquals('DELETE', $transport->last_method);
        $this->assertEquals('https://stir.example/api/v1/api-keys/k1', $transport->last_url);
    }
}

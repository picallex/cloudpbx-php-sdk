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

class StirshakenCdrTest extends TestCase
{
    private function fakeTransport($body, $status_code = 200)
    {
        return new class ($body, $status_code) implements \Cloudpbx\Protocol\Http\Client {
            /** @var string */
            public $last_url;
            /** @var string */
            public $last_method;
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
            ['id' => 1, 'call_id' => 'abc', 'from_uri' => 'sip:1@x', 'to_uri' => 'sip:2@y', 'source_ip' => '1.2.3.4', 'call_direction' => 'outbound'],
        ]));
        $client = $this->clientWith($transport);

        $cdrs = $client->cdrs->all();

        $this->assertEquals('GET', $transport->last_method);
        $this->assertEquals('https://stir.example/api/v1/cdrs', $transport->last_url);
        $this->assertCount(1, $cdrs);
        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\Stirshaken\Cdr::class, $cdrs[0]);
        $this->assertEquals('abc', $cdrs[0]->call_id);
        $this->assertEquals('outbound', $cdrs[0]->call_direction);
    }

    public function testStatsReturnsRawObject(): void
    {
        $transport = $this->fakeTransport(json_encode(['total' => 42, 'signed' => 40, 'validated' => 38]));
        $client = $this->clientWith($transport);

        $stats = $client->cdrs->stats();

        $this->assertEquals('GET', $transport->last_method);
        $this->assertEquals('https://stir.example/api/v1/cdrs/stats', $transport->last_url);
        $this->assertEquals(42, $stats['total']);
        $this->assertEquals(40, $stats['signed']);
    }
}

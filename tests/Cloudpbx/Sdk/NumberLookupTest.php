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

class NumberLookupTest extends TestCase
{
    /**
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

    /**
     * @param object $transport
     * @return Client
     */
    private function clientWith($transport)
    {
        return new Client(new ProtocolHTTP('https://api.example.com', 'KEY', $transport));
    }

    /**
     * @return array<string, mixed>
     */
    private function carrierRow()
    {
        return [
            'number' => '+14159929960',
            'carrier' => 'AT&T',
            'carrier_key' => 'att',
            'line_type' => 'mobile',
            'region' => 'US',
            'provider' => 'twilio',
            'queried_at' => '2026-06-15T22:06:36Z'
        ];
    }

    public function testAllListsCarriers(): void
    {
        $transport = $this->fakeTransport(json_encode(['data' => [$this->carrierRow()]]));
        $client = $this->clientWith($transport);

        $rows = $client->numberLookup->all();

        $this->assertEquals('GET', $transport->last_method);
        $this->assertEquals('https://api.example.com/api/v1/management/number_lookup', $transport->last_url);
        $this->assertCount(1, $rows);
        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\NumberLookup::class, $rows[0]);
        $this->assertEquals('AT&T', $rows[0]->carrier);
        $this->assertEquals('mobile', $rows[0]->line_type);
    }

    public function testAllAppendsFilters(): void
    {
        $transport = $this->fakeTransport(json_encode(['data' => []]));
        $client = $this->clientWith($transport);

        $client->numberLookup->all(['carrier' => 'att', 'limit' => 10]);

        $this->assertEquals(
            'https://api.example.com/api/v1/management/number_lookup?carrier=att&limit=10',
            $transport->last_url
        );
    }

    public function testShowEncodesNumber(): void
    {
        $transport = $this->fakeTransport(json_encode(['data' => $this->carrierRow()]));
        $client = $this->clientWith($transport);

        $row = $client->numberLookup->show('+14159929960');

        $this->assertEquals(
            'https://api.example.com/api/v1/management/number_lookup/%2B14159929960',
            $transport->last_url
        );
        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\NumberLookup::class, $row);
        $this->assertEquals('+14159929960', $row->number);
        $this->assertEquals('twilio', $row->provider);
    }
}

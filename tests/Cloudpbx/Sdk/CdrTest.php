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

class CdrTest extends TestCase
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

    public function testTraceBuildsQueryString(): void
    {
        $transport = $this->fakeTransport(json_encode(['data' => []]));
        $client = $this->clientWith($transport);

        $client->cdr->trace('7569b9ab-4202-409f-a7a4-5bdbb757abe4', 1387);

        $this->assertEquals('GET', $transport->last_method);
        $this->assertEquals(
            'https://api.example.com/api/v1/root/cdr/trace?recorduuid=7569b9ab-4202-409f-a7a4-5bdbb757abe4&customer_id=1387',
            $transport->last_url
        );
    }

    public function testTraceReturnsModelWithPayload(): void
    {
        $payload = [
            'recorduuid' => '7569b9ab-4202-409f-a7a4-5bdbb757abe4',
            'customer_id' => 1387,
            'duration' => 42,
            'legs' => [['uuid' => 'a', 'state' => 'hangup']]
        ];
        $transport = $this->fakeTransport(json_encode(['data' => $payload]));
        $client = $this->clientWith($transport);

        $trace = $client->cdr->trace('7569b9ab-4202-409f-a7a4-5bdbb757abe4', 1387);

        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\CdrTrace::class, $trace);
        $this->assertEquals('7569b9ab-4202-409f-a7a4-5bdbb757abe4', $trace->recorduuid);
        $this->assertEquals(1387, $trace->customer_id);
        $this->assertEquals(42, $trace->data['duration']);
        $this->assertEquals('hangup', $trace->data['legs'][0]['state']);
    }

    public function testTraceKeepsIdentifiersWhenApiOmitsThem(): void
    {
        // backend returns trace data without echoing recorduuid/customer_id
        $transport = $this->fakeTransport(json_encode(['data' => ['duration' => 10]]));
        $client = $this->clientWith($transport);

        $trace = $client->cdr->trace('7569b9ab-4202-409f-a7a4-5bdbb757abe4', 1387);

        $this->assertEquals('7569b9ab-4202-409f-a7a4-5bdbb757abe4', $trace->recorduuid);
        $this->assertEquals(1387, $trace->customer_id);
        $this->assertEquals(10, $trace->data['duration']);
    }

    public function testTraceRejectsNonIntegerCustomerId(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $transport = $this->fakeTransport(json_encode(['data' => []]));
        $client = $this->clientWith($transport);

        /** @phpstan-ignore-next-line intentional wrong type */
        $client->cdr->trace('7569b9ab-4202-409f-a7a4-5bdbb757abe4', '1387');
    }
}

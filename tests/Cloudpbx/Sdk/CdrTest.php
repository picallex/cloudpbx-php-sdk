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

    /**
     * respuesta real del endpoint (objeto en la raiz, sin envelope "data").
     *
     * @return array<string, mixed>
     */
    private function tracePayload()
    {
        return [
            'agent' => null,
            'answered' => false,
            'audio_quality' => ['mos' => 4.5, 'rating' => 'Bueno', 'score_pct' => 100],
            'direction' => 'inbound',
            'duration_sec' => 22,
            'ended_at' => '2026-06-15T22:06:58Z',
            'events' => [
                ['at' => '2026-06-15T22:06:36Z', 'description' => 'recibio una llamada entrante'],
                ['at' => '2026-06-15T22:06:58Z', 'description' => 'colgada antes de ser atendida']
            ],
            'from' => '17863578926',
            'hangup_cause' => 'NORMAL_CLEARING',
            'recording' => 'cloudpbx-17863057605-06152026-xxxx.wav',
            'started_at' => '2026-06-15T22:06:36Z',
            'to' => '17863057605',
            'type' => 'international'
        ];
    }

    public function testTraceBuildsQueryString(): void
    {
        $transport = $this->fakeTransport(json_encode([]));
        $client = $this->clientWith($transport);

        $client->cdr->trace('7569b9ab-4202-409f-a7a4-5bdbb757abe4', 1387);

        $this->assertEquals('GET', $transport->last_method);
        $this->assertEquals(
            'https://api.example.com/api/v1/root/cdr/trace?recorduuid=7569b9ab-4202-409f-a7a4-5bdbb757abe4&customer_id=1387',
            $transport->last_url
        );
    }

    public function testTraceMapsTypedFields(): void
    {
        // este endpoint devuelve el objeto en la raiz, no envuelto en {"data": ...}
        $transport = $this->fakeTransport(json_encode($this->tracePayload()));
        $client = $this->clientWith($transport);

        $trace = $client->cdr->trace('7da15607-bd5c-4da7-951a-cad9fb162712', 1387);

        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\CdrTrace::class, $trace);
        $this->assertEquals('inbound', $trace->direction);
        $this->assertEquals('international', $trace->type);
        $this->assertEquals('17863578926', $trace->from);
        $this->assertEquals('17863057605', $trace->to);
        $this->assertFalse($trace->answered);
        $this->assertEquals(22, $trace->duration_sec);
        $this->assertEquals('NORMAL_CLEARING', $trace->hangup_cause);
        $this->assertEquals(4.5, $trace->audio_quality->mos);
        $this->assertEquals('Bueno', $trace->audio_quality->rating);
        $this->assertNull($trace->agent);
        $this->assertCount(2, $trace->events);
        $this->assertEquals('2026-06-15T22:06:36Z', $trace->events[0]['at']);
    }

    public function testTraceInjectsIdentifiersNotReturnedByApi(): void
    {
        // el backend no devuelve recorduuid/customer_id en el payload
        $transport = $this->fakeTransport(json_encode($this->tracePayload()));
        $client = $this->clientWith($transport);

        $trace = $client->cdr->trace('7da15607-bd5c-4da7-951a-cad9fb162712', 1387);

        $this->assertEquals('7da15607-bd5c-4da7-951a-cad9fb162712', $trace->recorduuid);
        $this->assertEquals(1387, $trace->customer_id);
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

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

class SmartCalleridTest extends TestCase
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
     * respuesta real del endpoint, envuelta en {meta, data}.
     *
     * @return array<string, mixed>
     */
    private function statusPayload()
    {
        return [
            'meta' => ['dialouts' => 1, 'customers' => 1],
            'data' => [
                [
                    'customer_id' => 1387,
                    'customer_name' => 'acme',
                    'dialout_id' => 42,
                    'dialout_name' => 'usa-outbound',
                    'callerid_strategy' => 'random',
                    'smart_callerid_enabled' => true,
                    'callerid_group_id' => 7,
                    'callerid_group_name' => 'pool-usa',
                    'callerids' => [
                        [
                            'number' => '14155550001',
                            'name' => 'cid-spam',
                            'status' => 'active',
                            'spam_carriers' => ['tmobile'],
                            'reputation' => [
                                [
                                    'carrier' => 'tmobile',
                                    'status' => 'spam',
                                    'score' => 0.5,
                                    'source' => 'test',
                                    'checked_at' => '2026-08-01T10:00:00Z'
                                ],
                                [
                                    'carrier' => 'att',
                                    'status' => 'clean',
                                    'score' => 0.1,
                                    'source' => 'test',
                                    'checked_at' => '2026-08-01T10:00:00Z'
                                ]
                            ]
                        ],
                        [
                            'number' => '14155550002',
                            'name' => 'cid-clean',
                            'status' => 'active',
                            'spam_carriers' => []
                        ]
                    ]
                ]
            ]
        ];
    }

    public function testStatusRequestsTheReportWithoutFilters(): void
    {
        $transport = $this->fakeTransport(json_encode(['meta' => [], 'data' => []]));
        $client = $this->clientWith($transport);

        $result = $client->smartCallerid->status();

        $this->assertEquals('GET', $transport->last_method);
        $this->assertEquals(
            'https://api.example.com/api/v1/management/smart_callerid/status',
            $transport->last_url
        );
        $this->assertEquals([], $result);
    }

    public function testStatusScopesByCustomer(): void
    {
        $transport = $this->fakeTransport(json_encode(['data' => []]));
        $client = $this->clientWith($transport);

        $client->smartCallerid->status(1387);

        $this->assertEquals(
            'https://api.example.com/api/v1/management/smart_callerid/status?customer_id=1387',
            $transport->last_url
        );
    }

    public function testStatusAppendsVerbose(): void
    {
        $transport = $this->fakeTransport(json_encode(['data' => []]));
        $client = $this->clientWith($transport);

        $client->smartCallerid->status(null, true);

        $this->assertEquals(
            'https://api.example.com/api/v1/management/smart_callerid/status?verbose=true',
            $transport->last_url
        );
    }

    public function testStatusCombinesCustomerAndVerbose(): void
    {
        $transport = $this->fakeTransport(json_encode(['data' => []]));
        $client = $this->clientWith($transport);

        $client->smartCallerid->status(1387, true);

        $this->assertEquals(
            'https://api.example.com/api/v1/management/smart_callerid/status?customer_id=1387&verbose=true',
            $transport->last_url
        );
    }

    public function testStatusMapsTypedFields(): void
    {
        $transport = $this->fakeTransport(json_encode($this->statusPayload()));
        $client = $this->clientWith($transport);

        $report = $client->smartCallerid->status();

        $this->assertCount(1, $report);
        $entry = $report[0];
        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\SmartCalleridDialout::class, $entry);
        $this->assertEquals(1387, $entry->customer_id);
        $this->assertEquals('acme', $entry->customer_name);
        $this->assertEquals(42, $entry->dialout_id);
        $this->assertEquals('usa-outbound', $entry->dialout_name);
        $this->assertEquals('random', $entry->callerid_strategy);
        $this->assertTrue($entry->smart_callerid_enabled);
        $this->assertEquals(7, $entry->callerid_group_id);
        $this->assertEquals('pool-usa', $entry->callerid_group_name);
        $this->assertCount(2, $entry->callerids);
        $this->assertEquals('14155550001', $entry->callerids[0]['number']);
        $this->assertEquals(['tmobile'], $entry->callerids[0]['spam_carriers']);
        $this->assertCount(2, $entry->callerids[0]['reputation']);
        $this->assertEquals([], $entry->callerids[1]['spam_carriers']);
        $this->assertArrayNotHasKey('reputation', $entry->callerids[1]);
    }

    public function testStatusRejectsNonIntegerCustomerId(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $transport = $this->fakeTransport(json_encode(['data' => []]));
        $client = $this->clientWith($transport);

        /** @phpstan-ignore-next-line intentional wrong type */
        $client->smartCallerid->status('1387');
    }
}

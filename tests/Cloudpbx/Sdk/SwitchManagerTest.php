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

class SwitchManagerTest extends TestCase
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
        return new Client(new ProtocolHTTP('https://api.example.com', 'KEY', $transport));
    }

    /**
     * @param array<string, mixed> $over
     * @return array<string, mixed>
     */
    private function dialoutPayload($over = [])
    {
        return array_merge([
            'customer_id' => 1387,
            'customer_name' => 'acme',
            'dialout_id' => 42,
            'dialout_name' => 'usa-outbound',
            'destination' => '1XXXX',
            'gateway_strategy' => 'sequence',
            'gateways' => [
                [
                    'id' => 10,
                    'gateway_id' => 5,
                    'gateway_name' => 'telinta',
                    'realm' => 'sip.vip2phone.net',
                    'weight' => 100,
                    'strip' => '',
                    'prepend' => '',
                    'try_when_fail_with' => null,
                ],
                [
                    'id' => 11,
                    'gateway_id' => 6,
                    'gateway_name' => 'commio',
                    'realm' => 'sip.commio.net',
                    'weight' => 0,
                    'strip' => '',
                    'prepend' => '',
                    'try_when_fail_with' => null,
                ],
            ],
        ], $over);
    }

    public function testOverviewRequestsTheReportWithoutFilters(): void
    {
        $transport = $this->fakeTransport(json_encode(['meta' => [], 'data' => []]));
        $client = $this->clientWith($transport);

        $result = $client->switchManager->overview();

        $this->assertEquals('GET', $transport->last_method);
        $this->assertEquals(
            'https://api.example.com/api/v1/management/switch_manager/overview',
            $transport->last_url
        );
        $this->assertEquals([], $result);
    }

    public function testOverviewScopesByCustomer(): void
    {
        $transport = $this->fakeTransport(json_encode(['data' => []]));
        $client = $this->clientWith($transport);

        $client->switchManager->overview(1387);

        $this->assertEquals(
            'https://api.example.com/api/v1/management/switch_manager/overview?customer_id=1387',
            $transport->last_url
        );
    }

    public function testOverviewMapsDialoutsWithNestedGateways(): void
    {
        $transport = $this->fakeTransport(json_encode(['data' => [$this->dialoutPayload()]]));
        $client = $this->clientWith($transport);

        $report = $client->switchManager->overview();

        $this->assertCount(1, $report);
        $entry = $report[0];
        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\SwitchManagerDialout::class, $entry);
        $this->assertEquals(1387, $entry->customer_id);
        $this->assertEquals('acme', $entry->customer_name);
        $this->assertEquals(42, $entry->dialout_id);
        $this->assertEquals('usa-outbound', $entry->dialout_name);
        $this->assertEquals('1XXXX', $entry->destination);
        $this->assertEquals('sequence', $entry->gateway_strategy);

        $this->assertCount(2, $entry->gateways);
        $this->assertEquals(5, $entry->gateways[0]['gateway_id']);
        $this->assertEquals('telinta', $entry->gateways[0]['gateway_name']);
        $this->assertEquals(100, $entry->gateways[0]['weight']);
        $this->assertEquals(0, $entry->gateways[1]['weight']);
    }

    public function testOverviewRejectsNonIntegerCustomerId(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $transport = $this->fakeTransport(json_encode(['data' => []]));
        $client = $this->clientWith($transport);

        /** @phpstan-ignore-next-line intentional wrong type */
        $client->switchManager->overview('1387');
    }
}

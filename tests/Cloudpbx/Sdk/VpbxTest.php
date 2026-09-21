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

class VpbxTest extends TestCase
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
     * @return array<string, mixed>
     */
    private function overviewPayload()
    {
        return [
            'freeswitches' => [
                [
                    'id' => 34,
                    'name' => 'fs-a',
                    'switchname' => 'fs-a.internal',
                    'multitenant' => true,
                    'enabled_for_provisioning' => true,
                    's3_concurrency' => 4,
                    'customers_count' => 1,
                    'extensions_count' => 12,
                    'customers' => [
                        [
                            'id' => 2313,
                            'name' => 'acme',
                            'domain' => 'acme.myflexpbx.com',
                            'extensions_count' => 12,
                            'active' => true,
                        ],
                    ],
                ],
            ],
            'totals' => [
                'freeswitches_count' => 1,
                'customers_count' => 1,
                'extensions_count' => 12,
            ],
            'recent_movements' => [
                [
                    'id' => 900,
                    'customer_id' => 2313,
                    'domain' => 'acme.myflexpbx.com',
                    'freeswitch_id' => 34,
                    'switchname' => 'fs-a.internal',
                    'status' => 'succeeded',
                    'error_code' => null,
                    'error_message' => null,
                    'origin' => 'admin_api',
                    'started_at' => '2026-09-21T00:00:00Z',
                    'finished_at' => '2026-09-21T00:00:01Z',
                    'inserted_at' => '2026-09-21T00:00:00Z',
                    'updated_at' => '2026-09-21T00:00:01Z',
                ],
            ],
        ];
    }

    public function testOverviewRequestsTheReport(): void
    {
        $transport = $this->fakeTransport(json_encode(['data' => $this->overviewPayload()]));
        $client = $this->clientWith($transport);

        $client->vpbx->overview();

        $this->assertEquals('GET', $transport->last_method);
        $this->assertEquals(
            'https://api.example.com/api/v1/management/vpbx/overview',
            $transport->last_url
        );
    }

    public function testOverviewMapsFreeswitchesTotalsAndRecentMovements(): void
    {
        $transport = $this->fakeTransport(json_encode(['data' => $this->overviewPayload()]));
        $client = $this->clientWith($transport);

        $overview = $client->vpbx->overview();

        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\VpbxOverview::class, $overview);

        $this->assertCount(1, $overview->freeswitches);
        $fs = $overview->freeswitches[0];
        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\VpbxFreeswitch::class, $fs);
        $this->assertEquals(34, $fs->id);
        $this->assertEquals('fs-a', $fs->name);
        $this->assertTrue($fs->multitenant);
        $this->assertEquals(1, $fs->customers_count);
        $this->assertEquals(12, $fs->extensions_count);
        $this->assertCount(1, $fs->customers);
        $this->assertEquals(2313, $fs->customers[0]['id']);
        $this->assertEquals('acme.myflexpbx.com', $fs->customers[0]['domain']);

        $this->assertEquals(1, $overview->totals['freeswitches_count']);
        $this->assertEquals(12, $overview->totals['extensions_count']);

        $this->assertCount(1, $overview->recent_movements);
        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\ProvisioningAttempt::class, $overview->recent_movements[0]);
        $this->assertEquals(900, $overview->recent_movements[0]->id);
        $this->assertEquals('succeeded', $overview->recent_movements[0]->status);
    }
}

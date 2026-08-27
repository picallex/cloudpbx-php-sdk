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

class DialoutGatewayTest extends TestCase
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
        return new Client(new ProtocolHTTP('https://api.example.com', 'KEY', $transport));
    }

    /**
     * @param array<string, mixed> $over
     * @return array<string, mixed>
     */
    private function rowPayload($over = [])
    {
        return array_merge([
            'id' => 10,
            'customer_id' => 1387,
            'dialout_id' => 42,
            'gateway_id' => 5,
            'strip' => '',
            'prepend' => '',
            'weight' => 100,
            'try_when_fail_with' => null,
        ], $over);
    }

    public function testAllListsGatewaysOfDialout(): void
    {
        $transport = $this->fakeTransport(json_encode(['data' => [
            $this->rowPayload(),
            $this->rowPayload(['id' => 11, 'gateway_id' => 6, 'weight' => 0]),
        ]]));
        $client = $this->clientWith($transport);

        $rows = $client->dialoutGateways->all(1387, 42);

        $this->assertEquals('GET', $transport->last_method);
        $this->assertEquals(
            'https://api.example.com/api/v1/management/customers/1387/dialouts/42/gateways',
            $transport->last_url
        );
        $this->assertCount(2, $rows);
        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\DialoutGateway::class, $rows[0]);
        $this->assertEquals(5, $rows[0]->gateway_id);
        $this->assertEquals(100, $rows[0]->weight);
        $this->assertEquals(0, $rows[1]->weight);
    }

    public function testCreateSendsDialoutGatewayBody(): void
    {
        $transport = $this->fakeTransport(json_encode(['data' => $this->rowPayload()]));
        $client = $this->clientWith($transport);

        $row = $client->dialoutGateways->create(1387, 42, ['gateway_id' => 5, 'weight' => 100]);

        $this->assertEquals('POST', $transport->last_method);
        $this->assertEquals(
            'https://api.example.com/api/v1/management/customers/1387/dialouts/42/gateways',
            $transport->last_url
        );
        $sent = json_decode((string) $transport->last_body, true);
        $this->assertEquals(['dialout_gateway' => ['gateway_id' => 5, 'weight' => 100]], $sent);
        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\DialoutGateway::class, $row);
    }

    public function testUpdateSendsWeightToMemberUrl(): void
    {
        $transport = $this->fakeTransport(json_encode(['data' => $this->rowPayload(['weight' => 0])]));
        $client = $this->clientWith($transport);

        $client->dialoutGateways->update(1387, 42, 10, ['weight' => 0]);

        $this->assertEquals('PUT', $transport->last_method);
        $this->assertEquals(
            'https://api.example.com/api/v1/management/customers/1387/dialouts/42/gateways/10',
            $transport->last_url
        );
        $sent = json_decode((string) $transport->last_body, true);
        $this->assertEquals(['dialout_gateway' => ['weight' => 0]], $sent);
    }

    public function testDeleteHitsMemberUrl(): void
    {
        $transport = $this->fakeTransport(json_encode(['data' => []]));
        $client = $this->clientWith($transport);

        $client->dialoutGateways->delete(1387, 42, 10);

        $this->assertEquals('DELETE', $transport->last_method);
        $this->assertEquals(
            'https://api.example.com/api/v1/management/customers/1387/dialouts/42/gateways/10',
            $transport->last_url
        );
    }

    public function testAllRejectsNonIntegerCustomerId(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $client = $this->clientWith($this->fakeTransport(json_encode(['data' => []])));

        /** @phpstan-ignore-next-line intentional wrong type */
        $client->dialoutGateways->all('1387', 42);
    }
}

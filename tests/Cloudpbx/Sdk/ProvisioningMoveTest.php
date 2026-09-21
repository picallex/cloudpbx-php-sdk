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

class ProvisioningMoveTest extends TestCase
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

    public function testMovePatchesTheCustomerProvisioning(): void
    {
        $body = json_encode(['data' => [
            'customer_id' => 2313,
            'domain' => 'acme.myflexpbx.com',
            'switchname' => 'fs-a.internal',
        ]]);
        $transport = $this->fakeTransport($body);
        $client = $this->clientWith($transport);

        $provisioning = $client->provisioning->move(2313, 34);

        $this->assertEquals('PATCH', $transport->last_method);
        $this->assertEquals(
            'https://api.example.com/api/v1/management/customers/2313/provisioning',
            $transport->last_url
        );
        $this->assertEquals(['freeswitch_id' => 34], json_decode($transport->last_body, true));

        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\Provisioning::class, $provisioning);
        $this->assertEquals(2313, $provisioning->customer_id);
        $this->assertEquals('fs-a.internal', $provisioning->switchname);
    }

    public function testMoveRejectsNonIntegerArguments(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $transport = $this->fakeTransport(json_encode(['data' => []]));
        $client = $this->clientWith($transport);

        /** @phpstan-ignore-next-line intentional wrong type */
        $client->provisioning->move(2313, 'x');
    }
}

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

class GroupTest extends TestCase
{
    /**
     * fake transport que cuenta los requests y devuelve un grupo.
     *
     * @return object
     */
    private function fakeTransport()
    {
        return new class () implements \Cloudpbx\Protocol\Http\Client {
            /** @var int */
            public $requests = 0;

            public function sendRequest(Request $request): Response
            {
                $this->requests++;
                return new ResponseFromArray(json_encode(['data' => [
                    'id' => 10, 'customer_id' => 1, 'name' => 'ventas', 'alias' => 'ventas', 'users' => []
                ]]), 201);
            }
        };
    }

    public function testCreateWithoutCalleridGroupFailsBeforeCallingApi(): void
    {
        $transport = $this->fakeTransport();
        $client = new Client(new ProtocolHTTP('https://api.example.com', 'KEY', $transport));

        try {
            $client->groups->create(1, ['name' => 'ventas', 'alias' => 'ventas']);
            $this->fail('expected InvalidArgumentException');
        } catch (\InvalidArgumentException $e) {
            $this->assertStringContainsString('callerid_group_id', $e->getMessage());
        }

        $this->assertSame(0, $transport->requests);
    }

    public function testCreateWithCalleridGroup(): void
    {
        $transport = $this->fakeTransport();
        $client = new Client(new ProtocolHTTP('https://api.example.com', 'KEY', $transport));

        $group = $client->groups->create(1, ['name' => 'ventas', 'alias' => 'ventas', 'callerid_group_id' => 7]);

        $this->assertSame(1, $transport->requests);
        $this->assertSame(10, $group->id);
    }
}

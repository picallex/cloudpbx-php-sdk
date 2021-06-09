<?php
// Copyright 2021 Picallex Holding Group. All rights reserved.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Cloudpbx\Protocol;
use Cloudpbx\Util;

class ClientCurlTest extends TestCase
{

    protected function setUp(): void
    {
        $base = Util\Environment::get('test', 'cloudpbx_api_base');
        $api_key = Util\Environment::get('test', 'cloudpbx_api_key');

        $transport = new Protocol\Http\Implementation\ClientCurl();
        $protocol = new Protocol\ProtocolHTTP($base, $api_key, $transport);

        $this->client = new \Cloudpbx\Sdk\Client($protocol);
    }

    /**
     * @vcr query_all_customers
     */
    public function testQueryAllCustomers(): array
    {
        $customers = $this->client->customers->all();
        $this->assertIsArray($customers);

        $customer = $customers[0];
        $this->assertTrue($customer->hasAttribute('id'));
        $this->assertTrue($customer->hasAttribute('name'));
        $this->assertTrue($customer->hasAttribute('domain'));

        return [$customer];
    }

    /**
     * @vcr query_one_customer
     * @depends testQueryAllCustomers
     */
    public function testQueryOneCustomer(array $stack): void
    {
        $last_customer = array_pop($stack);
        $customer = $this->client->customers->show($last_customer->id);

        $this->assertEquals($last_customer->name, $customer->name);
        $this->assertEquals($last_customer->id, $customer->id);
    }
}

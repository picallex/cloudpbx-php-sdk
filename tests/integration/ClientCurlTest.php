<?php
// Copyright 2021 Picallex Holding Group. All rights reserved.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Cloudpbx\Http\Implementation;
use Cloudpbx\Sdk\Util;

class ClientCurlTest extends TestCase
{

    protected function setUp(): void
    {
        $base = Util\Environment::get('test', 'cloudpbx_api_base');
        $api_key = Util\Environment::get('test', 'cloudpbx_api_key');

        $protocol = new Implementation\ProtocolJson($api_key);
        $transport = new Implementation\ClientCurl($base, $protocol);
        $this->client = new \Cloudpbx\Sdk\Client($transport);
    }

    /**
     * @vcr query_all_customers
     */
    public function testQueryAllCustomers(): void
    {
        $customers = $this->client->customers->all();
        $this->assertIsArray($customers);

        $customer = $customers[0];
        $this->assertTrue($customer->hasAttribute('id'));
        $this->assertTrue($customer->hasAttribute('name'));
        $this->assertTrue($customer->hasAttribute('domain'));
    }
}

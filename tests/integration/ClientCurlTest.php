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

        $protocol = Protocol\ProtocolHTTP::createWithDefaultClient($base, $api_key);

        $this->client = new \Cloudpbx\Sdk\Client($protocol);
    }

    /**
     * @vcr query_all_customers
     */
    public function testQueryAllCustomers(): array
    {
        $customers = $this->client->customers->all();
        $this->assertIsArray($customers);

        $customer = $customers[1];
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

    /**
     * @vcr query_all_users_by_customer
     * @depends testQueryAllCustomers
     */
    public function testQueryAllUsers(array $stack): array
    {
        $last_customer = array_pop($stack);

        $users = $this->client->users->all($last_customer->id);
        $this->assertIsArray($users);
        $this->assertGreaterThan(1, count($users));

        $user = $users[0];
        $this->assertTrue($user->hasAttribute('id'));
        $this->assertTrue($user->hasAttribute('caller_name'));
        $this->assertTrue($user->hasAttribute('caller_number'));
        $this->assertTrue($user->hasAttribute('accountcode'));
        $this->assertTrue($user->hasAttribute('alias'));

        return [[$last_customer, $user]];
    }

    /**
     * @vcr query_one_user_by_customer
     * @depends testQueryAllUsers
     */
    public function testQueryOneUser(array $stack): void
    {
        [$last_customer, $last_user] = array_pop($stack);
        $user = $this->client->users->show($last_customer->id, $last_user->id);

        $this->assertEquals($user->id, $last_user->id);
    }

    /**
     * @vcr query_callcenter_queues_by_customer
     * @depends testQueryAllCustomers
     */
    public function testQueryAllCallcenterQueue(array $stack): void
    {
        $last_customer = array_pop($stack);

        $queues = $this->client->callcenterQueues->all($last_customer->id);
        $this->assertIsArray($queues);
        $this->assertGreaterThan(1, count($queues));

        $queue = $queues[0];
        $this->assertTrue($queue->hasAttribute('id'));
        $this->assertTrue($queue->hasAttribute('alias'));
        $this->assertTrue($queue->hasAttribute('name'));
        $this->assertTrue($queue->hasAttribute('strategy'));
    }

    /**
     * @vcr query_dialout_by_customer
     * @depends testQueryAllCustomers
     */
    public function testQueryAllDialout(array $stack): void
    {
        $last_customer = array_pop($stack);

        $dialouts = $this->client->dialouts->all($last_customer->id);
        $this->assertIsArray($dialouts);
        $this->assertGreaterThan(1, count($dialouts));

        $dialout = $dialouts[0];

        $this->assertTrue($dialout->hasAttribute('id'));
        $this->assertTrue($dialout->hasAttribute('destination'));
        $this->assertTrue($dialout->hasAttribute('gateway_strategy'));
        $this->assertTrue($dialout->hasAttribute('callerid_strategy'));
        $this->assertTrue($dialout->hasAttribute('strip'));
        $this->assertTrue($dialout->hasAttribute('prepend'));
        $this->assertTrue($dialout->hasAttribute('weight'));
    }

    /**
     * @vcr query_router_dids_by_customer
     * @depends testQueryAllCustomers
     */
    public function testQueryAllRouterDids(array $stack): void
    {
        $last_customer = array_pop($stack);

        $routers = $this->client->routerDids->all($last_customer->id);
        $this->assertIsArray($routers);
        $this->assertGreaterThan(1, count($routers));

        $router = $routers[0];
        $this->assertTrue($router->hasAttribute('id'));
        $this->assertTrue($router->hasAttribute('did'));
        // optional attributes:
        // - callcenter_queue_id
        // - user_id
        // - ivr_menu_id
        // - dialout_id
    }

    /**
     * @vcr query_firewall_ipset
     */
    public function testQueryAllFirewallIpSet(): void
    {
        $ipsets = $this->client->firewallIpSets->all();
        $this->assertIsArray($ipsets);
        $this->assertGreaterThan(0, count($ipsets));


        $ipset = $ipsets[0];
        $this->assertTrue($ipset->hasAttribute('status'));
        $this->assertTrue($ipset->hasAttribute('cidr_block'));
    }
}

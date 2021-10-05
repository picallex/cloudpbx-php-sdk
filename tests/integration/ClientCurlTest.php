<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Cloudpbx\Protocol;
use Cloudpbx\Util;

class ClientCurlTest extends TestCase
{
    private static $customer_id;
    private static $callcenter_queue_id = 0;
    private static $follow_me_id = 0;

    protected function setUp(): void
    {
        $base = Util\Environment::get('test', 'cloudpbx_api_base');
        $api_key = Util\Environment::get('test', 'cloudpbx_api_key');
        self::$customer_id = (int)Util\Environment::get('test', 'cloudpbx_customer_id');
        self::$callcenter_queue_id = (int)Util\Environment::get('test', 'cloudpbx_callcenter_queue_id', 0);
        self::$follow_me_id = (int)Util\Environment::get('test', 'cloudpbx_follow_me_id', 0);

        $this->client = \Cloudpbx\Sdk::createDefaultClient($base, $api_key);
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
     */
    public function testQueryOneCustomer(): array
    {
        $customer = $this->client->customers->show(self::$customer_id);

        $this->assertTrue($customer->hasAttribute('id'));
        $this->assertTrue($customer->hasAttribute('name'));

        return [$customer];
    }

    /**
     * @vcr query_all_users_by_customer
     * @depends testQueryOneCustomer
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
     * @depends testQueryOneCustomer
     */
    public function testQueryAllCallcenterQueue(array $stack): array
    {
        $last_customer = array_pop($stack);

        $queues = $this->client->callcenterQueues->all($last_customer->id);
        $this->assertIsArray($queues);
        $this->assertGreaterThan(1, count($queues));

        $queue = $queues[0];
        $this->assertTrue($queue->hasAttribute('id'));
        $this->assertTrue($queue->hasAttribute('customer_id'));
        $this->assertTrue($queue->hasAttribute('alias'));
        $this->assertTrue($queue->hasAttribute('name'));
        $this->assertTrue($queue->hasAttribute('strategy'));
        $this->assertTrue($queue->hasAttribute('max_wait_time'));

        $this->assertGreaterThanOrEqual(0, $queue->max_wait_time);

        if (self::$callcenter_queue_id > 0) {
            foreach ($queues as $found_queue) {
                if ($found_queue->id == self::$callcenter_queue_id) {
                    $queue = $found_queue;
                    break;
                }
            }
        }
        return [[$last_customer, $queue]];
    }

    /**
     * @vcr query_dialout_by_customer
     * @depends testQueryOneCustomer
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

        $customer = $this->client->preload($dialout->customer);
        $this->assertEquals($customer->id, $last_customer->id);
    }

    /**
     * @vcr query_router_dids_by_customer
     * @depends testQueryOneCustomer
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

        $this->assertFalse(is_null($router->has_one));
        $this->assertFalse(is_null($router->has_one->model));
        $this->assertFalse(is_null($router->has_one->id));

        // optional attributes:
        // - callcenter_queue_id
        // - user_id
        // - ivr_menu_id
        // - dialout_id
        $relation = $this->client->preload($router->has_one);
        $this->assertTrue($relation->hasAttribute('id'));
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

    /**
     * @vcr create_firewall_ipset
     */
    public function testAddIpToFirewall(): array
    {
        $cidr_block = '4.4.4.4/32';
        $status = 'whitelist';

        $ipset = $this->client->firewallIpSets->create($cidr_block, $status);

        $this->assertTrue($ipset->hasAttribute('cidr_block'));
        $this->assertTrue($ipset->hasAttribute('status'));

        $this->assertEquals($cidr_block, $ipset->cidr_block);
        $this->assertEquals($status, $ipset->status);

        return [$cidr_block];
    }

    /**
     * @vcr delete_firewall_ipset
     * @depends testAddIpToFirewall
     */
    public function testDeleteIpFromFirewall(array $stack): void
    {
        $cidr_block = array_pop($stack);
        $this->client->firewallIpSets->delete($cidr_block);

        $this->assertTrue(true, 'expected not throw exception');
    }

    /**
     * @vcr query_all_follow_me_by_customer
     * @depends testQueryOneCustomer
     */
    public function testQueryAllFollowMe(array $stack): array
    {
        $customer = array_pop($stack);

        $follows = $this->client->followMes->all($customer->id);

        $this->assertIsArray($follows);
        $this->assertGreaterThan(0, count($follows));

        $follow = $follows[0];
        $this->assertTrue($follow->hasAttribute('id'));
        $this->assertTrue($follow->hasAttribute('customer_id'));
        $this->assertTrue($follow->hasAttribute('name'));

        return [$follow];
    }

    /**
     * @vcr query_all_follow_me_entries
     * @depends testQueryAllFollowMe
     */
    public function testQueryAllFollowMeEntries(): void
    {
        $entries = $this->client->followMeEntries->all(self::$customer_id, self::$follow_me_id);

        $this->assertIsArray($entries);
        $this->assertGreaterThan(0, count($entries));

        $entry = $entries[0];
        $this->assertTrue($entry->hasAttribute('id'));
        $this->assertTrue($entry->hasAttribute('follow_me_id'));
    }

    /**
     * @vcr query_all_ivr_menu_by_customer
     */
    public function testQueryAllIvrMenu(): array
    {
        $this->markTestIncomplete(
            'This test not have staging environment'
        );

        $ivrmenus = $this->client->ivrMenus->all(self::$customer_id);

        $this->assertIsArray($ivrmenus);
        $this->assertGreaterThan(0, count($ivrmenus));

        $ivrmenu = $ivrmenus[0];
        $this->assertTrue($ivrmenu->hasAttribute('id'));
        $this->assertTrue($ivrmenu->hasAttribute('name'));
        $this->assertEquals(self::$customer_id, $ivrmenu->customer->id);

        return [$ivrmenu];
    }

    /**
     * @vcr query_all_ivr_menu_entries
     * @depends testQueryAllIvrMenu
     */
    public function testQueryAllIvrMenuEntries(array $stack): void
    {
        $this->markTestIncomplete(
            'This test not have staging environment'
        );

        $ivrmenu = array_pop($stack);

        $entries = $this->client->ivrMenuEntries->all(self::$customer_id, $ivrmenu->id);

        $this->assertIsArray($entries);
        $this->assertGreaterThan(0, count($entries));

        $entry = $entries[0];
        $this->assertTrue($entry->hasAttribute('id'));
        $this->assertTrue($entry->hasAttribute('digits'));
        $this->assertTrue($entry->hasAttribute('param'));
        $this->assertTrue($entry->hasAttribute('action'));
    }

    /**
     * @vcr query_all_tiers
     * @depends testQueryOneCustomer
     */
    public function testQueryAllTiers(array $stack): void
    {
        $customer = array_pop($stack);

        $tiers = $this->client->callcenterQueues->tiers($customer->id);
        $this->assertIsArray($tiers);
        $this->assertGreaterThan(0, count($tiers));

        $tier = $tiers[0];
        $this->assertTrue($tier->hasAttribute('id'));
        $this->assertTrue($tier->hasAttribute('customer_id'));
        $this->assertTrue($tier->hasAttribute('callcenter_queue_id'));
        $this->assertTrue($tier->hasAttribute('callcenter_agent_id'));
    }

    /**
     * @vcr query_all_agents
     * @depends testQueryAllCallcenterQueue
     */
    public function testQuerayAllAgents(array $stack): void
    {
        [$customer, $queue] = array_pop($stack);

        $agents = $this->client->callcenterAgents->all($customer->id);

        $this->assertIsArray($agents);
        $this->assertGreaterThan(0, count($agents));

        $agent = $agents[0];
        $this->assertTrue($agent->hasAttribute('id'));
        $this->assertTrue($agent->hasAttribute('user_id'));
        $this->assertTrue($agent->hasAttribute('customer_id'));
        $this->assertTrue($agent->hasAttribute('autologin'));
    }

    /**
     * @vcr query_all_agents_by_queue
     * @depends testQueryAllCallcenterQueue
     */
    public function testQuerayAllAgentsByQueue(array $stack): void
    {
        [$customer, $queue] = array_pop($stack);

        $agents = $this->client->callcenterQueues->agents($customer->id, $queue->id);

        $this->assertIsArray($agents);
        $this->assertGreaterThan(0, count($agents));

        $agent = $agents[0];
        $this->assertTrue($agent->hasAttribute('id'));
        $this->assertTrue($agent->hasAttribute('user_id'));
        $this->assertTrue($agent->hasAttribute('customer_id'));
        $this->assertTrue($agent->hasAttribute('autologin'));
    }

    /**
     * @vcr query_all_agents_preloads
     * @depends testQueryAllCallcenterQueue
     */
    public function testPreloadRelationUsingCallcenterAgent(array $stack): void
    {
        [$customer, $queue] = array_pop($stack);
        $agents = $this->client->callcenterQueues->agents($customer->id, $queue->id);

        $agent = $agents[0];
        $loaded_customer = $this->client->preload($agent->customer);

        $this->assertEquals($loaded_customer->id, $customer->id);
        $this->assertEquals($loaded_customer->name, $customer->name);
    }

    /**
     * @vcr query_blacklist
     */
    public function testQueryAllBlacklist(): void
    {
        $lists = $this->client->blacklists->all(self::$customer_id);

        $this->assertIsArray($lists);
        $this->assertGreaterThan(0, count($lists));

        $blacklist = $lists[0];
        $this->assertTrue($blacklist->hasAttribute('id'));
        $this->assertTrue($blacklist->hasAttribute('customer_id'));
        $this->assertTrue($blacklist->hasAttribute('number'));

        $customer = $this->client->preload($blacklist->customer);
        $this->assertEquals($customer->id, self::$customer_id);
    }

    /**
     * @vcr query_all_sounds
     */
    public function testQueryAllSound(): void
    {
        $sounds = $this->client->sounds->all(self::$customer_id);

        $this->assertIsArray($sounds);
        $this->assertGreaterThan(0, count($sounds));

        $sound = $sounds[0];
        $this->assertTrue($sound->hasAttribute('id'));
        $this->assertTrue($sound->hasAttribute('customer_id'));
        $this->assertTrue($sound->hasAttribute('name'));
        $this->assertTrue($sound->hasAttribute('template'));
        $this->assertTrue($sound->hasAttribute('usage'));

        $new_sound = $this->client->sounds->show(self::$customer_id, $sound->id);
        $this->assertEquals($new_sound->id, $sound->id);
        $this->assertEquals($new_sound->customer_id, $sound->customer_id);
    }

    /**
     * @vcr query_all_supervisors
     */
    public function testQueryAllSupervisor(): void
    {
        $supervisors = $this->client->supervisors->all(self::$customer_id);

        $this->assertIsArray($supervisors);
        $this->assertGreaterThan(0, count($supervisors));

        $supervisor = $supervisors[0];
        $this->assertTrue($supervisor->hasAttribute('id'));
        $this->assertTrue($supervisor->hasAttribute('customer_id'));
        $this->assertTrue($supervisor->hasAttribute('user_id'));
    }


    /**
     * @vcr create_customer
     */
    public function testCreateCustomerWithMinimalData(): array
    {
        $customer = $this->client->customers->create([
            'name' => 'bob',
            'domain' => 'bob.org'
        ]);

        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\Customer::class, $customer);
        $this->assertTrue($customer->hasAttribute('id'));
        $this->assertTrue($customer->id > 0);
        $this->assertTrue($customer->name == 'bob');
        $this->assertTrue($customer->domain == 'bob.org');

        return [$customer];
    }

    /**
     * @vcr update_customer
     * @depends testCreateCustomerWithMinimalData
     */
    public function testUpdateCustomer(array $stack): void
    {
        $customer = array_pop($stack);

        $customer_updated = $this->client->customers->update($customer->id, [
            'limit_external_calls' => 66
        ]);

        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\Customer::class, $customer_updated);
        $this->assertEquals(66, $customer_updated->limit_external_calls);
        $this->assertEquals($customer->name, $customer_updated->name);
        $this->assertEquals($customer->domain, $customer_updated->domain);
    }

    /**
     * @vcr create_full_customer
     */
    public function testCreateCustomerWithFullData(): void
    {
        $customer = $this->client->customers->create([
            'name' => 'bobfull2',
            'domain' => 'bobfull2.org',
            'limit_external_calls' => 22,
            'accountcode' => 'BOBABCD'
        ]);

        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\Customer::class, $customer);
        $this->assertTrue($customer->hasAttribute('id'));
        $this->assertTrue($customer->id > 0);
        $this->assertTrue($customer->name == 'bobfull2');
        $this->assertTrue($customer->domain == 'bobfull2.org');
        $this->assertTrue($customer->limit_external_calls == 22);
        $this->assertTrue($customer->accountcode == 'BOBABCD');
    }

    /**
     * @vcr create_user
     * @depends testCreateCustomerWithMinimalData
     */
    public function testCreateUserWithMinimalData($stack): array
    {
        $customer = array_pop($stack);

        $user = $this->client->users->create($customer->id, [
            'name' => 'simpon',
            'password' => 'insecure537537',
            'is_webrtc' => false
        ]);

        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\User::class, $user);
        $this->assertTrue($user->id > 0);
        $this->assertEquals('simpon', $user->name);
        $this->assertEquals(false, $user->is_webrtc);

        return [$user];
    }

    /**
     * @vcr create_full_user
     * @depends testCreateCustomerWithMinimalData
     */
    public function testCreateUserWithFullData($stack): void
    {
        $customer = array_pop($stack);

        $user = $this->client->users->create($customer->id, [
            'name' => 'simpon5full',
            'password' => 'insecure537537',
            'alias' => 'description',
            'accountcode' => 'invoice',
            'caller_name' => 'caller-name',
            'caller_number' => 'caller-number',
            'dnd_on_sip_unregister' => true,
            'available_on_sip_register' => true,
            'is_webrtc' => true
        ]);

        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\User::class, $user);
        $this->assertTrue($user->id > 0);
        $this->assertEquals('simpon5full', $user->name);
        $this->assertEquals(true, $user->is_webrtc);
        $this->assertEquals('description', $user->alias);
        $this->assertEquals('invoice', $user->accountcode);
        $this->assertEquals('caller-name', $user->caller_name);
        $this->assertEquals('caller-number', $user->caller_number);
        $this->assertEquals(true, $user->dnd_on_sip_unregister);
        $this->assertEquals(true, $user->available_on_sip_register);
    }

    /**
     * @vcr update_user
     * @depends testCreateUserWithMinimalData
     */
    public function testUpdateUser($stack): void
    {
        $user = array_pop($stack);

        $user_updated = $this->client->users->update($user->customer_id, $user->id, [

            'alias' => 'new alias'
        ]);

        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\User::class, $user);
        $this->assertTrue($user->id > 0);
        $this->assertEquals($user->name, $user_updated->name);
        $this->assertEquals('new alias', $user_updated->alias);
    }

    /**
     * @vcr delete_user
     * @depends testCreateCustomerWithMinimalData
     */
    public function testDeleteUser(array $stack): void
    {
        $customer = array_pop($stack);

        $user = $this->client->users->create($customer->id, [
            'name' => 'simpondelete',
            'password' => 'insecure537537',
            'is_webrtc' => false
        ]);

        $this->client->users->delete($user->customer_id, $user->id);

        try {
            $this->client->users->show($user->customer_id, $user->id);
        } catch(Exception $e) {
            $this->assertInstanceOf(\Cloudpbx\Protocol\Error\NotFoundError::class, $e);
        }
    }

    /**
     * @vcr create_router_did_to_user
     * @depends testQueryOneCustomer
     */
    public function testCreateRouterDidToUser(array $stack): void
    {
        $customer = array_pop($stack);

        $user = $this->client->users->create($customer->id, [
            'name' => 'simpon5fullrouter',
            'password' => 'insecure537537',
            'alias' => 'description',
            'accountcode' => 'invoice',
            'caller_name' => 'caller-name',
            'caller_number' => 'caller-number',
            'dnd_on_sip_unregister' => true,
            'available_on_sip_register' => true,
            'is_webrtc' => true
        ]);

        $route = $this->client->routerDids->route_to_user($customer->id, $user->id, '12345678');
        $this->assertEquals('12345678', $route->did);
        $this->assertEquals($user->id, $route->user_id);
        $this->assertEquals($customer->id, $route->customer_id);
    }

    /**
     * @vcr delete_router_did
     * @depends testQueryOneCustomer
     */
    public function testDeleteRouterDid(array $stack): void
    {
        // Given a route
        $customer = array_pop($stack);
        $user = $this->client->users->create($customer->id, [
            'name' => 'simpon5fullrouterdelete',
            'password' => 'insecure537537',
            'alias' => 'description',
            'accountcode' => 'invoice',
            'caller_name' => 'caller-name',
            'caller_number' => 'caller-number',
            'dnd_on_sip_unregister' => true,
            'available_on_sip_register' => true,
            'is_webrtc' => true
        ]);

        // When route exists
        $route = $this->client->routerDids->route_to_user($customer->id, $user->id, '12345678');

        // Then delete it
        $this->client->routerDids->delete($customer->id, $route->id);
        $this->assertTrue(true);
    }
}

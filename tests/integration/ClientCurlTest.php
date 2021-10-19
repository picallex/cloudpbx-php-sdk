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
     * @vcr create_callcenter_queue
     * @depends testQueryOneCustomer
     */
    public function testCreateCallcenterQueue(array $stack): void
    {
        $customer = array_pop($stack);

        $queue = $this->client->callcenterQueues->create($customer->id, [
            'name' => 'superqueue',
            'strategy' => 'random', //round-robin, ring-all
            'max_wait_time' => 5,
            'description' => 'a super queue',
            'alias' => 'queuesuper'
        ]);

        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\CallcenterQueue::class, $queue);
        $this->assertEquals('superqueue', $queue->name);
        $this->assertEquals('random', $queue->strategy);
        $this->assertEquals(5, $queue->max_wait_time);
        $this->assertEquals('a super queue', $queue->description);
        $this->assertEquals('queuesuper', $queue->alias);
    }

    /**
     * @vcr delete_callcenter_queue
     * @depends testQueryOneCustomer
     */
    public function testDeleteCallcenterQueue(array $stack): void
    {
        $customer = array_pop($stack);

        $queue = $this->client->callcenterQueues->create($customer->id, [
            'name' => 'superqueue53535',
            'strategy' => 'random', //round-robin, ring-all
            'max_wait_time' => 5,
            'description' => 'a super queue',
            'alias' => 'queuesuper'
        ]);


        $this->client->callcenterQueues->delete($customer->id, $queue->id);
        try {
            $this->client->callcenterQueues->show($customer->id, $queue->id);
        } catch(Exception $e) {
            $this->assertInstanceOf(\Cloudpbx\Protocol\Error\NotFoundError::class, $e);
        }
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
     * @vcr create_router_did_to_callcenter_queue
     * @depends testQueryOneCustomer
     */
    public function testCreateRouterDidToCallcenterQueue(array $stack): void
    {
        $customer = array_pop($stack);
        $queue = $this->client->callcenterQueues->create($customer->id, [
            'name' => 'superqueue53535routerdid',
            'strategy' => 'random', //round-robin, ring-all
            'max_wait_time' => 5,
            'description' => 'a super queue',
            'alias' => 'queuesuper'
        ]);

        $route = $this->client->routerDids->route_to_callcenter_queue($customer->id, $queue->id, '123456789');

        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\RouterDid::class, $route);
        $this->assertEquals('123456789', $route->did);
        $this->assertEquals($queue->id, $route->callcenter_queue_id);
        $this->assertEquals($customer->id, $route->customer_id);
    }

    /**
     * @vcr create_router_did_to_ivr_menu
     * @depends testQueryOneCustomer
     */
    public function testCreateRouterDidToIvrMenu(array $stack): void
    {
        $customer = array_pop($stack);
        $ivr = $this->client->ivrMenus->create($customer->id, [
            'name' => 'test-ivr-mune-route-did',
            'description' => 'test ivr menu'
        ]);

        $route = $this->client->routerDids->route_to_ivr_menu($customer->id, $ivr->id, '1234567890');

        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\RouterDid::class, $route);
        $this->assertEquals('1234567890', $route->did);
        $this->assertEquals($ivr->id, $route->ivr_menu_id);
        $this->assertEquals($customer->id, $route->customer_id);
    }

    /**
     * @vcr create_router_did_to_follow_me
     * @depends testQueryOneCustomer
     */
    public function testCreateRouterDidToFollowMe(array $stack): void
    {
        $customer = array_pop($stack);
        $me = $this->client->followMes->create($customer->id, [
            'name' => 'test-follow-me-route-did',
        ]);

        $route = $this->client->routerDids->route_to_follow_me($customer->id, $me->id, '12345678901');

        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\RouterDid::class, $route);
        $this->assertEquals('12345678901', $route->did);
        $this->assertEquals($me->id, $route->follow_me_id);
        $this->assertEquals($customer->id, $route->customer_id);
    }

    /**
     * @vcr create_router_did_to_destination_number
     * @depends testQueryOneCustomer
     */
    public function testCreateRouterDidToDestination(array $stack): void
    {
        $customer = array_pop($stack);

        $route = $this->client->routerDids->route_to_destination_number($customer->id, '12345678901', '656565');

        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\RouterDid::class, $route);
        $this->assertEquals('12345678901', $route->did);
        $this->assertEquals('656565', $route->destination_number);
        $this->assertEquals($customer->id, $route->customer_id);
    }

    /**
     * @vcr create_router_did_to_dialout
     * @depends testQueryOneCustomer
     */
    public function testCreateRouterDidToDialout(array $stack): void
    {
        $customer = array_pop($stack);

        $dialout = $this->client->dialouts->create($customer->id, [
            'weight' => 100,
            'strip' => '999',
            'prepend' => '8888',
            'name' => 'international-route',
            'destination' => '1XXXXX.',
            'gateway_strategy' => 'sequence',
            'callerid_strategy' => 'random'
        ]);

        $route = $this->client->routerDids->route_to_dialout($customer->id, $dialout->id, '6565655', '17885566');

        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\RouterDid::class, $route);
        $this->assertEquals('6565655', $route->did);
        $this->assertEquals('17885566', $route->destination_number);
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

    /**
     * @vcr create_ivr_menu
     * @depends testQueryOneCustomer
     */
    public function testCreateIvrMenu(array $stack): void
    {
        $customer = array_pop($stack);

        $ivr = $this->client->ivrMenus->create($customer->id, [
            'name' => 'test-ivr-mune',
            'description' => 'test ivr menu'
        ]);

        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\IvrMenu::class, $ivr);
        $this->assertTrue($ivr->id > 0);
        $this->assertEquals('test-ivr-mune', $ivr->name);
        $this->assertEquals('test ivr menu', $ivr->description);
    }

    /**
     * @vcr delete_ivr_menu
     * @depends testQueryOneCustomer
     */
    public function testDeleteIvrMenu(array $stack): void
    {
        $customer = array_pop($stack);

        $ivr = $this->client->ivrMenus->create($customer->id, [
            'name' => 'test-ivr-mune-delete',
            'description' => 'test ivr menu'
        ]);

        $this->client->ivrMenus->delete($customer->id, $ivr->id);

        try {
            $this->client->ivrMenus->show($customer->id, $ivr->id);
        } catch(Exception $e) {
            $this->assertInstanceOf(\Cloudpbx\Protocol\Error\NotFoundError::class, $e);
        }
    }

    /**
     * @vcr create_follow_me
     * @depends testQueryOneCustomer
     */
    public function testCreateFollowMe(array $stack): void
    {
        $customer = array_pop($stack);

        $me = $this->client->followMes->create($customer->id, [
            'name' => 'test-follow-me',
            'ringback_type' => 'fake_ring', //bridge_media
        ]);

        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\FollowMe::class, $me);
        $this->assertTrue($me->id > 0);
        $this->assertEquals('test-follow-me', $me->name);
        $this->assertEquals($customer->id, $me->customer_id);
    }

    /**
     * @vcr delete_follow_me
     * @depends testQueryOneCustomer
     */
    public function testDeleteFollowMe(array $stack): void
    {
        $customer = array_pop($stack);

        $me = $this->client->followMes->create($customer->id, [
            'name' => 'test-follow-me-delete',
        ]);

        $this->client->followMes->delete($customer->id, $me->id);

        try {
            $this->client->followMes->show($customer->id, $me->id);
        } catch(Exception $e) {
            $this->assertInstanceOf(\Cloudpbx\Protocol\Error\NotFoundError::class, $e);
        }
    }

    /**
     * @vcr create_dialout
     * @depends testQueryOneCustomer
     */
    public function testCreateDialout(array $stack): void
    {
        $customer = array_pop($stack);

        $dialout = $this->client->dialouts->create($customer->id, [
            'weight' => 100,
            'strip' => '999',
            'prepend' => '8888',
            'name' => 'international',
            'destination' => '1XXXXX.',
            'gateway_strategy' => 'sequence',
            'callerid_strategy' => 'random'
        ]);

        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\Dialout::class, $dialout);
        $this->assertTrue($dialout->id > 0);
        $this->assertEquals('international', $dialout->name);
        $this->assertEquals('1XXXXX.', $dialout->destination);
        $this->assertEquals('sequence', $dialout->gateway_strategy);
        $this->assertEquals('random', $dialout->callerid_strategy);
    }


    /**
     * @vcr delete_dialout
     * @depends testQueryOneCustomer
     */
    public function testDeleteDialout(array $stack): void
    {
        $customer = array_pop($stack);

        $dialout = $this->client->dialouts->create($customer->id, [
            'weight' => 100,
            'strip' => '999',
            'prepend' => '8888',
            'name' => 'international-delete',
            'destination' => '1XXXXX.',
            'gateway_strategy' => 'sequence',
            'callerid_strategy' => 'random'
        ]);


        $this->client->dialouts->delete($customer->id, $dialout->id);
        try {
            $this->client->dialouts->show($customer->id, $dialout->id);
        } catch (Exception $e) {
            $this->assertInstanceOf(\Cloudpbx\Protocol\Error\NotFoundError::class, $e);
        }
    }


    /**
     * @vcr create_callerid_group
     * @depends testQueryOneCustomer
     */
    public function testCreateCalleridGroup(array $stack): void
    {
        $customer = array_pop($stack);

        $record = $this->client->calleridGroups->create($customer->id, [
            'name' => 'bob callerid'
        ]);

        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\CalleridGroup::class, $record);
        $this->assertTrue($record->id > 0);
        $this->assertEquals('bob callerid', $record->name);

        $remote_record = $this->client->calleridGroups->show($customer->id, $record->id);
        $this->assertEquals($record->id, $remote_record->id);
    }

    /**
     * @vcr update_callerid_group
     * @depends testQueryOneCustomer
     */
    public function testUpdateCalleridGroup(array $stack): void
    {
        $customer = array_pop($stack);

        $record = $this->client->calleridGroups->create($customer->id, [
            'name' => 'bob callerid new'
        ]);

        $new_record = $this->client->calleridGroups->update($customer->id, $record->id, ['name' => 'bob callerid updated']);

        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\CalleridGroup::class, $new_record);
        $this->assertEquals($record->id, $new_record->id);
        $this->assertEquals('bob callerid updated', $new_record->name);
    }

    /**
     * @vcr delete_callerid_group
     * @depends testQueryOneCustomer
     */
    public function testDeleteCalleridGroup(array $stack): void
    {
        $customer = array_pop($stack);

        $record = $this->client->calleridGroups->create($customer->id, [
            'name' => 'bob callerid to delete'
        ]);

        $this->client->calleridGroups->delete($customer->id, $record->id);

        try {
            $this->client->calleridGroups->show($customer->id, $record->id);
        } catch(Exception $e) {
            $this->assertInstanceOf(\Cloudpbx\Protocol\Error\NotFoundError::class, $e);
        }
    }


    /**
     * @vcr list_callerid_group
     * @depends testQueryOneCustomer
     */
    public function testListCalleridGroup(array $stack): void
    {
        $customer = array_pop($stack);

        $this->client->calleridGroups->create($customer->id, [
            'name' => 'bob callerid list'
        ]);

        $records = $this->client->calleridGroups->all($customer->id);

        $this->assertTrue(count($records) > 0);

        $record = $records[0];
        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\CalleridGroup::class,
 $record);
    }

    /**
     * @vcr create_callerid
     * @depends testQueryOneCustomer
     */
    public function testCreateCallerid(array $stack): void
    {
        $customer = array_pop($stack);

        $callerid_group = $this->client->calleridGroups->create($customer->id, [
            'name' => 'bob callerid for callerid'
        ]);


        $callerid = $this->client->callerids->create($customer->id, $callerid_group->id, [
            'name' => 'callerid name',
            'number' => 'callerid number',
            'weight' => 100
        ]);

        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\Callerid::class, $callerid);
        $this->assertTrue($callerid->id > 0);
        $this->assertEquals($callerid_group->id, $callerid->callerid_group_id);
        $this->assertEquals('callerid number', $callerid->number);
        $this->assertEquals('callerid name', $callerid->name);
        $this->assertEquals(100, $callerid->weight);
    }


    /**
     * @vcr update_callerid
     * @depends testQueryOneCustomer
     */
    public function testUpdateCallerid(array $stack): void
    {
        $customer = array_pop($stack);

        $callerid_group = $this->client->calleridGroups->create($customer->id, [
            'name' => 'bob callerid for callerid update'
        ]);


        $callerid = $this->client->callerids->create($customer->id, $callerid_group->id, [
            'name' => 'callerid name new',
            'number' => 'callerid number new',
            'weight' => 100
        ]);

        $callerid_updated = $this->client->callerids->update($customer->id, $callerid_group->id, $callerid->id, [
            'name' => 'callerid name updated',
            'number' => 'callerid number updated',
            'weight' => 88
        ]);

        $this->assertEquals($callerid->id, $callerid_updated->id);
        $this->assertEquals('callerid name updated', $callerid_updated->name);
        $this->assertEquals('callerid number updated', $callerid_updated->number);
        $this->assertEquals(88, $callerid_updated->weight);
    }


    /**
     * @vcr delete_callerid
     * @depends testQueryOneCustomer
     */
    public function testDeleteCallerid(array $stack): void
    {
        $customer = array_pop($stack);

        $callerid_group = $this->client->calleridGroups->create($customer->id, [
            'name' => 'bob callerid for callerid delete'
        ]);


        $callerid = $this->client->callerids->create($customer->id, $callerid_group->id, [
            'name' => 'callerid name new',
            'number' => 'callerid number new',
            'weight' => 100
        ]);

        $this->client->callerids->delete($customer->id, $callerid_group->id, $callerid->id);

        try {
            $this->client->callerids->show($customer->id, $callerid_group->id, $callerid->id);
        } catch(Exception $e) {
            $this->assertInstanceOf(\Cloudpbx\Protocol\Error\NotFoundError::class, $e);
        }
    }

    /**
     * @vcr create_callerid
     * @depends testQueryOneCustomer
     */
    public function testListCallerid(array $stack): void
    {
        $customer = array_pop($stack);

        $callerid_group = $this->client->calleridGroups->create($customer->id, [
            'name' => 'bob callerid for callerid list'
        ]);


        $this->client->callerids->create($customer->id, $callerid_group->id, [
            'name' => 'callerid name list',
            'number' => 'callerid number list',
            'weight' => 100
        ]);

        $records = $this->client->callerids->all($customer->id, $callerid_group->id);

        $this->assertTrue(count($records) > 0);

        $record = $records[0];
        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\Callerid::class,
 $record);
    }


    /**
     * if the system not found DialoutGroup, then use callerid_group
     * from dialout.
     *
     * @vcr create_dialout_with_callerid_group
     * @depends testQueryOneCustomer
     */
    public function testCreateDialoutWithCalleridGroup(array $stack): void
    {

        $customer = array_pop($stack);

        $callerid_group = $this->client->calleridGroups->create($customer->id, [
            'name' => 'bob callerid for callerid dialout'
        ]);

        $dialout = $this->client->dialouts->create($customer->id, [
            'weight' => 100,
            'strip' => '999',
            'prepend' => '8888',
            'name' => 'international',
            'destination' => '1XXXXX.',
            'gateway_strategy' => 'sequence',
            'callerid_strategy' => 'random',
            'callerid_group_id' => $callerid_group->id
        ]);

        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\Dialout::class, $dialout);
        $this->assertTrue($dialout->id > 0);
        $this->assertEquals($callerid_group->id, $dialout->callerid_group_id);

        $cidg = $this->client->preload($dialout->callerid_group);
        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\CalleridGroup::class, $cidg);
        $this->assertEquals($cidg->id, $dialout->callerid_group_id);
    }

    /**
     * @vcr create_group
     * @depends testQueryOneCustomer
     */
    public function testCreateGroup(array $stack): void
    {
        $customer = array_pop($stack);

        $group = $this->client->groups->create($customer->id, [
            'name' => 'system-name-group',
            'alias' => 'bob group'
        ]);

        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\Group::class, $group);
        $this->assertTrue($group->id > 0);
        $this->assertEquals('system-name-group', $group->name);
        $this->assertEquals('bob group', $group->alias);
    }

    /**
     * @vcr update_group
     * @depends testQueryOneCustomer
     */
    public function testUpdateGroup($stack): void
    {
        $customer = array_pop($stack);

        $group = $this->client->groups->create($customer->id, [
            'name' => 'system-name-group new',
            'alias' => 'bob group new'
        ]);

        $group_updated = $this->client->groups->update($group->customer_id, $group->id, [
            'name' => 'system name updated',
            'alias' => 'bob group updated'
        ]);

        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\Group::class, $group);
        $this->assertEquals($group->id, $group_updated->id);
        $this->assertEquals('system name updated', $group_updated->name);
        $this->assertEquals('bob group updated', $group_updated->alias);
    }

    /**
     * @vcr delete_group
     * @depends testQueryOneCustomer
     */
    public function testDeleteGroup(array $stack): void
    {
        $customer = array_pop($stack);

        $group = $this->client->groups->create($customer->id, [
            'name' => 'system-name-group create',
            'alias' => 'bob group create'
        ]);

        $this->client->groups->delete($customer->id, $group->id);

        try {
            $this->client->groups->show($customer->id, $group->id);
        } catch(Exception $e) {
            $this->assertInstanceOf(\Cloudpbx\Protocol\Error\NotFoundError::class, $e);
        }
    }

    /**
     * @vcr list_group
     * @depends testQueryOneCustomer
     */
    public function testListGroup(array $stack): void
    {
        $customer = array_pop($stack);

        $group = $this->client->groups->create($customer->id, [
            'name' => 'system-name-group list',
            'alias' => 'bob group list'
        ]);

        $records = $this->client->groups->all($customer->id);
        $this->assertTrue(count($records) > 0);


        $record = $records[0];
        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\Group::class, $record);
    }

    /**
     * @vcr create_user
     * @depends testCreateCustomerWithMinimalData
     */
    public function testAttachDetachUserToOrFromGroup(array $stack): void
    {
        $customer = array_pop($stack);

        $user = $this->client->users->create($customer->id, [
            'name' => 'user to attach',
            'password' => 'insecure537537ThEuhoecru5353}',
            'is_webrtc' => false
        ]);

        $group = $this->client->groups->create($customer->id, [
            'name' => 'group example attach',
        ]);

        $this->client->groups->attach_user($customer->id, $group->id, $user->id);
        $this->client->groups->detach_user($customer->id, $group->id, $user->id);

        try {
            $this->client->groups->detach_user($customer->id, $group->id, $user->id);
        } catch(Exception $e) {
            $this->assertInstanceOf(\Cloudpbx\Protocol\Error\NotFoundError::class, $e);
        }
        $this->assertTrue(true);
    }


    /**
     *
     * @vcr attach_to_dialout_callerid_group
     * @depends testQueryOneCustomer
     */
    public function testAttachToDialoutCalleridGroup(array $stack): void
    {

        $customer = array_pop($stack);

        $group = $this->client->groups->create($customer->id, [
            'name' => 'group id attach',
            'alias' => 'group id attach'
        ]);

        $callerid_group = $this->client->calleridGroups->create($customer->id, [
            'name' => 'bob callerid for callerid attach dialout callerid'
        ]);

        $dialout = $this->client->dialouts->create($customer->id, [
            'weight' => 100,
            'strip' => '9998',
            'prepend' => '8888',
            'name' => 'international',
            'destination' => '1XXXXX.',
            'gateway_strategy' => 'sequence',
            'callerid_strategy' => 'random',
        ]);

        $this->client->dialouts->attach_callerid_group($customer->id, $group->id, $dialout->id, $callerid_group->id);
        $this->client->dialouts->detach_callerid_group($customer->id, $group->id, $dialout->id, $callerid_group->id);


        $this->assertTrue(true);
    }

}

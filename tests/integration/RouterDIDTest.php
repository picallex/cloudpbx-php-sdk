<?php

/**
 * Copyright 2022 Picallex Holding Group. All rights reserved.
 *
 * @author (2022) Jovany Leandro G.C <jovany@picallex.com>
 */

declare(strict_types=1);

require_once('ClientTestCase.php');
use PHPUnit\Framework\TestCase;
use Cloudpbx\Protocol;
use Cloudpbx\Util;


class RouterDidTest extends ClientTestCase
{

    protected function localSetUp(): void
    {
        $this->customer =  $this->createDefaultCustomer([]);
    }

    public function testCreateRouterDidToUser(): void
    {
        $customer_id = $this->customer->id;
        $did = $this->randomDid();
        $user = $this->createDefaultUser($customer_id);

        $route = $this->client->routerDids->route_to_user($customer_id, $user->id, $did);

        $this->assertEquals($did, $route->did);
        $this->assertEquals($user->id, $route->user_id);
        $this->assertEquals($customer_id, $route->customer_id);
    }

    public function testCreateRouterDidToCallcenterQueue(): void
    {
        $customer_id = $this->customer->id;
        $did = $this->randomDid();
        $queue = $this->client->callcenterQueues->create($customer_id, [
            'name' => $this->generateRandomString(10),
            'strategy' => 'random', //round-robin, ring-all
            'max_wait_time' => 5,
            'description' => 'a super queue',
            'alias' => 'queuesuper'
        ]);

        $route = $this->client->routerDids->route_to_callcenter_queue($customer_id, $queue->id, $did);

        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\RouterDid::class, $route);
        $this->assertEquals($did, $route->did);
        $this->assertEquals($queue->id, $route->callcenter_queue_id);
        $this->assertEquals($customer_id, $route->customer_id);
    }

    public function testCreateRouterDidToIvrMenu(): void
    {
        $customer_id = $this->customer->id;
        $did = $this->randomDid();
        $ivr = $this->client->ivrMenus->create($customer_id, [
            'name' => $this->generateRandomString(10),
            'description' => 'test ivr menu'
        ]);

        $route = $this->client->routerDids->route_to_ivr_menu($customer_id, $ivr->id, $did);

        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\RouterDid::class, $route);
        $this->assertEquals($did, $route->did);
        $this->assertEquals($ivr->id, $route->ivr_menu_id);
        $this->assertEquals($customer_id, $route->customer_id);
    }

    public function testCreateRouterDidToFollowMe(): void
    {
        $customer_id = $this->customer->id;
        $did = $this->randomDid();
        $me = $this->client->followMes->create($customer_id, [
            'name' => 'test-follow-me-route-did',
        ]);

        $route = $this->client->routerDids->route_to_follow_me($customer_id, $me->id, $did);

        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\RouterDid::class, $route);
        $this->assertEquals($did, $route->did);
        $this->assertEquals($me->id, $route->follow_me_id);
        $this->assertEquals($customer_id, $route->customer_id);
    }

    public function testCreateRouterDidToDestination(): void
    {
        $customer_id = $this->customer->id;
        $did = $this->randomDid();
        $route = $this->client->routerDids->route_to_destination_number($customer_id, $did, '656565');

        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\RouterDid::class, $route);
        $this->assertEquals($did, $route->did);
        $this->assertEquals('656565', $route->destination_number);
        $this->assertEquals($customer_id, $route->customer_id);
    }


    public function testCreateRouterDidToDialout(): void
    {
        $customer_id = $this->customer->id;
        $did = $this->randomDid();
        $dialout = $this->client->dialouts->create($customer_id, [
            'weight' => 100,
            'strip' => '999',
            'prepend' => '8888',
            'name' => 'international-route',
            'destination' => '1XXXXX.',
            'gateway_strategy' => 'sequence',
            'callerid_strategy' => 'random'
        ]);

        $route = $this->client->routerDids->route_to_dialout($customer_id, $dialout->id, $did, '17885566');

        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\RouterDid::class, $route);
        $this->assertEquals($did, $route->did);
        $this->assertEquals('17885566', $route->destination_number);
        $this->assertEquals($customer_id, $route->customer_id);
    }

    public function testQueryAllRouterDids(): void
    {
        $customer = $this->createDefaultCustomer();
        $user = $this->createDefaultUser($customer->id);
        $did = $this->randomDid();
        $route = $this->client->routerDids->route_to_user($customer->id, $user->id, $did);

        $routers = $this->client->routerDids->all($customer->id);
        $this->assertIsArray($routers);
        $this->assertGreaterThan(0, count($routers));

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

    public function testDeleteRouterDid(): void
    {
        // Given
        $customer_id = $this->customer->id;
        $did = $this->randomDid();
        $user = $this->createDefaultUser($customer_id);

        // When route exists
        $route = $this->client->routerDids->route_to_user($customer_id, $user->id, $did);

        // Then delete it
        $this->client->routerDids->delete($customer_id, $route->id);
        $this->assertTrue(true);
    }

    private function randomDid(): string
    {
        return $this->generateRandomNumber(10);
    }
}

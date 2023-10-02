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

    public function testQueryAllRouterDids(): void
    {
        $customer = $this->createDefaultCustomer();
        $did = $this->randomDid();
        $route = $this->aRoute($customer->id, $did);

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
        // - follow_me_id
        $relation = $this->client->preload($router->has_one);
        $this->assertTrue($relation->hasAttribute('id'));
    }

    public function testDeleteRouterDid(): void
    {
        // Given
        $customer_id = $this->customer->id;
        $did = $this->randomDid();

        // When route exists
        $route = $this->aRoute($customer_id, $did);

        // Then delete it
        $this->client->routerDids->delete($customer_id, $route->id);
        $this->assertTrue(true);
    }

    private function randomDid(): string
    {
        return $this->generateRandomNumber(10);
    }

    private function aRoute($customer_id, $did) {
        $me = $this->client->followMes->create($customer_id, [
            'name' => 'test-follow-me-route-did',
        ]);

        return $this->client->routerDids->route_to_follow_me($customer_id, $me->id, $did);
    }
}

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


class CallcenterQueueTest extends ClientTestCase
{
    protected function localSetUp(): void
    {
        $this->customer = $this->createDefaultCustomer();
    }

    public function testCreateCallcenterQueue(): void
    {
        $customer_id = $this->customer->id;
        $queue = $this->client->callcenterQueues->create($customer_id, [
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

    public function testQueryAllCallcenterQueue(): void
    {
        $last_customer_id = $this->customer->id;
        $queue = $this->client->callcenterQueues->create($last_customer_id, [
            'name' => $this->generateRandomString(5),
            'strategy' => 'random', //round-robin, ring-all
            'max_wait_time' => 5,
            'description' => 'a super queue',
            'alias' => 'queuesuper'
        ]);

        $queues = $this->client->callcenterQueues->all($last_customer_id);
        $this->assertIsArray($queues);
        $this->assertGreaterThan(0, count($queues));
    }

    public function testUpdateCallcenterQueue(): void
    {
        $customer_id = $this->customer->id;
        $queue = $this->client->callcenterQueues->create($customer_id, [
            'name' => 'superqueuenew',
            'description' => 'newqueue',
            //strategy can be ring-all, longest-idle-agent, round-robin, top-down, agent-with-least-talk-time, agent-with-fewest-talk-time, sequentially-by-agent-order, random, ring-progressively
            'strategy' => 'round-robin',
            'max_wait_time' => 5,
        ]);

        $queue = $this->client->callcenterQueues->update($customer_id, $queue->id, [
            'name' => 'superqueueupdated',
            'description' => 'updated description',
            //strategy can be ring-all, longest-idle-agent, round-robin, top-down, agent-with-least-talk-time, agent-with-fewest-talk-time, sequentially-by-agent-order, random, ring-progressively
            'strategy' => 'ring-all',
            'max_wait_time' => 5,
        ]);

        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\CallcenterQueue::class, $queue);
        $this->assertEquals('superqueueupdated', $queue->name);
        $this->assertEquals('ring-all', $queue->strategy);
        $this->assertEquals(5, $queue->max_wait_time);
        $this->assertEquals('updated description', $queue->description);
    }

    public function testDeleteCallcenterQueue(): void
    {
        $customer_id = $this->customer->id;
        $queue = $this->client->callcenterQueues->create($customer_id, [
            'name' => 'superqueue53535',
            'strategy' => 'random', //round-robin, ring-all
            'max_wait_time' => 5,
            'description' => 'a super queue',
            'alias' => 'queuesuper'
        ]);


        $this->client->callcenterQueues->delete($customer_id, $queue->id);
        try {
            $this->client->callcenterQueues->show($customer_id, $queue->id);
        } catch(Exception $e) {
            $this->assertInstanceOf(\Cloudpbx\Protocol\Error\NotFoundError::class, $e);
        }
    }

    public function testCallcenterQueueAttachCallcenterAgent(): void
    {
        $customer = $this->customer;
        $user = $this->createDefaultUser($customer->id);
        $queue = $this->createDefaultCallcenterQueue($customer->id);

        $agent = $this->client->callcenterQueues->callcenter_agent_attach($customer->id, $queue->id, $user->id, ['autologin' => true, 'level' => 1, 'position' => 2]);

        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\CallcenterAgent::class, $agent);
        $this->assertEquals($customer->id, $agent->customer_id);
        $this->assertEquals($user->id, $agent->user_id);
        $this->assertEquals(true, $agent->autologin);
    }

    public function testCallcenterQueueDetachCallcenterAgent(): void
    {
        $customer = $this->customer;
        $user = $this->createDefaultUser($customer->id);
        $queue = $this->createDefaultCallcenterQueue($customer->id);

        $agent_attached = $this->client->callcenterQueues->callcenter_agent_attach($customer->id, $queue->id, $user->id, ['autologin' => false]);

        $agent = $this->client->callcenterQueues->callcenter_agent_detach($customer->id, $queue->id, $user->id);

        $this->assertEquals($agent_attached->id, $agent->id);
    }

    public function testPreloadRelationUsingCallcenterAgent(): void
    {
        $customer = $this->customer;
        $queue = $this->createDefaultCallcenterQueue($customer->id);
        $this->createDefaultCallcenterAgent($customer->id, $queue->id);

        $agents = $this->client->callcenterQueues->agents($customer->id, $queue->id);

        $agent = $agents[0];
        $loaded_customer = $this->client->preload($agent->customer);

        $this->assertEquals($loaded_customer->id, $customer->id);
        $this->assertEquals($loaded_customer->name, $customer->name);
    }


    public function testQuerayAllAgentsByQueue(): void
    {
        $customer = $this->customer;
        $queue = $this->createDefaultCallcenterQueue($customer->id);
        $this->createDefaultCallcenterAgent($customer->id, $queue->id);

        $agents = $this->client->callcenterQueues->agents($customer->id, $queue->id);

        $this->assertIsArray($agents);
        $this->assertGreaterThan(0, count($agents));

        $agent = $agents[0];
        $this->assertTrue($agent->hasAttribute('id'));
        $this->assertTrue($agent->hasAttribute('user_id'));
        $this->assertTrue($agent->hasAttribute('customer_id'));
        $this->assertTrue($agent->hasAttribute('autologin'));
    }

    public function testQuerayAllAgents(): void
    {
        $customer = $this->customer;
        $queue = $this->createDefaultCallcenterQueue($customer->id);
        $this->createDefaultCallcenterAgent($customer->id, $queue->id);

        $agents = $this->client->callcenterAgents->all($customer->id);

        $this->assertIsArray($agents);
        $this->assertGreaterThan(0, count($agents));

        $agent = $agents[0];
        $this->assertTrue($agent->hasAttribute('id'));
        $this->assertTrue($agent->hasAttribute('user_id'));
        $this->assertTrue($agent->hasAttribute('customer_id'));
        $this->assertTrue($agent->hasAttribute('autologin'));
    }

    public function testQueryAllTiers(): void
    {
        $customer = $this->customer;
        $queue = $this->createDefaultCallcenterQueue($customer->id);
        $agent = $this->createDefaultCallcenterAgent($customer->id, $queue->id, ['autologin' => true, 'level' => 1, 'position' => 2]);

        $tiers = $this->client->callcenterQueues->tiers($customer->id);
        $this->assertIsArray($tiers);
        $this->assertGreaterThan(0, count($tiers));

        $tier = $tiers[0];
        $this->assertTrue($tier->hasAttribute('id'));
        $this->assertTrue($tier->hasAttribute('customer_id'));
        $this->assertTrue($tier->hasAttribute('callcenter_queue_id'));
        $this->assertTrue($tier->hasAttribute('callcenter_agent_id'));
    }

    private function createDefaultCallcenterAgent($customer_id, $queue_id, $params = []) {
        $nparams = array_merge(['autologin' => false], $params);
        $user = $this->createDefaultUser($customer_id);

        return $this->client->callcenterQueues->callcenter_agent_attach($customer_id, $queue_id, $user->id, $nparams);
    }
}

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


class DialoutTest extends ClientTestCase
{
    protected function localSetUp(): void
    {
        $this->customer = $this->createDefaultCustomer();
    }

    public function testCreateDialout(): void
    {
        $customer_id = $this->customer->id;
        $dialout = $this->createDefaultDialout($customer_id, [
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


    public function testUpdateDialout(): void
    {
        $customer_id = $this->customer->id;
        $dialout = $this->createDefaultDialout($customer_id);
        $updated_dialout = $this->client->dialouts->update($customer_id, $dialout->id, [
            'weight' => 199,
            'strip' => '555',
            'prepend' => '666',
        ]);

        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\Dialout::class, $dialout);

        $this->assertEquals($updated_dialout->id, $dialout->id);
        $this->assertEquals($updated_dialout->strip, '555');
        $this->assertEquals($updated_dialout->prepend, '666');
    }

    public function testDeleteDialout(): void
    {
        $customer_id = $this->customer->id;
        $dialout = $this->createDefaultDialout($customer_id);

        $this->client->dialouts->delete($customer_id, $dialout->id);
        try {
            $this->client->dialouts->show($customer_id, $dialout->id);
        } catch (Exception $e) {
            $this->assertInstanceOf(\Cloudpbx\Protocol\Error\NotFoundError::class, $e);
        }
    }

    public function testQueryAllDialout(): void
    {
        $last_customer = $this->customer;
        $customer = $this->createDefaultCustomer();
        $dialout = $this->createDefaultDialout($last_customer->id);
        $dialouts = $this->client->dialouts->all($last_customer->id);
        $this->assertIsArray($dialouts);
        $this->assertGreaterThan(0, count($dialouts));

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
     * if the system not found DialoutGroup, then use callerid_group
     * from dialout.
     *
     */
    public function testCreateDialoutWithCalleridGroup(): void
    {

        $customer = $this->customer;
        $callerid_group = $this->createDefaultCalleridGroup($customer->id);
        $dialout = $this->createDefaultDialout($customer->id, ['callerid_group_id' => $callerid_group->id]);


        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\Dialout::class, $dialout);
        $this->assertTrue($dialout->id > 0);
        $this->assertEquals($callerid_group->id, $dialout->callerid_group_id);

        $cidg = $this->client->preload($dialout->callerid_group);
        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\CalleridGroup::class, $cidg);
        $this->assertEquals($cidg->id, $dialout->callerid_group_id);
    }

    public function testAttachToDialoutCalleridGroup(): void
    {

        $customer = $this->customer;
        $group = $this->createDefaultGroup($customer->id);
        $callerid_group = $this->createDefaultCalleridGroup($customer->id);
        $dialout = $this->createDefaultDialout($customer->id);

        $this->client->dialouts->attach_callerid_group($customer->id, $group->id, $dialout->id, $callerid_group->id);
        $this->client->dialouts->detach_callerid_group($customer->id, $group->id, $dialout->id, $callerid_group->id);


        $this->assertTrue(true);
    }
}

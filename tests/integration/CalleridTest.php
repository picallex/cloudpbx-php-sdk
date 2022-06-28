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

class CalleridTest extends ClientTestCase
{
    protected function localSetUp(): void
    {
        $this->customer = $this->createDefaultCustomer();
    }

    public function testCreateCallerid(): void
    {
        $customer = $this->customer;
        $callerid_group = $this->createDefaultCalleridGroup($customer->id);

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

    public function testUpdateCallerid(): void
    {
        $customer = $this->customer;
        $callerid_group = $this->createDefaultCalleridGroup($customer->id);


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

    public function testDeleteCallerid(): void
    {
        $customer = $this->customer;
        $callerid_group = $this->createDefaultCalleridGroup($customer->id);

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

    public function testListCallerid(): void
    {
        $customer = $this->customer;

        $callerid_group = $this->createDefaultCalleridGroup($customer->id);

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

}

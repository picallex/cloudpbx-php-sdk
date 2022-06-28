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

class CalleridGroupTest extends ClientTestCase
{
    protected function localSetUp(): void
    {
        $this->customer = $this->createDefaultCustomer([]);
    }

    public function testCreateCalleridGroup(): void
    {
        $customer = $this->customer;
        $name = $this->generateRandomName();

        $record = $this->client->calleridGroups->create($customer->id, [
            'name' => $name
        ]);

        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\CalleridGroup::class, $record);
        $this->assertTrue($record->id > 0);
        $this->assertEquals($name, $record->name);

        $remote_record = $this->client->calleridGroups->show($customer->id, $record->id);
        $this->assertEquals($record->id, $remote_record->id);
    }


    public function testUpdateCalleridGroup(): void
    {
        $customer = $this->customer;
        $new_name = $this->generateRandomName();
        $record = $this->createDefaultCalleridGroup($customer->id);

        $new_record = $this->client->calleridGroups->update($customer->id, $record->id, ['name' => $new_name]);

        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\CalleridGroup::class, $new_record);
        $this->assertEquals($record->id, $new_record->id);
        $this->assertEquals($new_name, $new_record->name);
    }

    public function testDeleteCalleridGroup(): void
    {
        $customer = $this->customer;
        $name = $this->generateRandomName();

        $record = $this->createDefaultCalleridGroup($customer->id);

        $this->client->calleridGroups->delete($customer->id, $record->id);

        try {
            $this->client->calleridGroups->show($customer->id, $record->id);
        } catch(Exception $e) {
            $this->assertInstanceOf(\Cloudpbx\Protocol\Error\NotFoundError::class, $e);
        }
    }

    public function testListCalleridGroup(): void
    {
        $customer = $this->customer;
        $this->createDefaultCalleridGroup($customer->id);

        $records = $this->client->calleridGroups->all($customer->id);

        $this->assertTrue(count($records) > 0);
        $record = $records[0];
        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\CalleridGroup::class,
 $record);
    }
}

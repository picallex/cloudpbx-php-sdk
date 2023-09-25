<?php

/**
 * Copyright 2022 Picallex Holding Group. All rights reserved.
 *
 * @author (2022) Jovany Leandro G.C <jovany@picallex.com>
 * @author (2023) Matias Damian Gomez <matias@picallex.com>
 */

declare(strict_types=1);

require_once('ClientTestCase.php');
use PHPUnit\Framework\TestCase;
use Cloudpbx\Protocol;
use Cloudpbx\Util;


class DialoutGroupTest extends ClientTestCase
{
    protected function localSetUp(): void
    {
        $this->customer = $this->createDefaultCustomer();
        $this->group = $this->createDefaultGroup($this->customer->id);
        $this->callerid_group = $this->createDefaultCalleridGroup($this->customer->id);
        $this->dialout = $this->createDefaultDialout($this->customer->id);
    }

    public function testQueryAllDialoutGroup(): void
    {
        $customer = $this->customer;
        $group = $this->group;
        $callerid_group = $this->callerid_group;
        $dialout = $this->dialout;

        $this->client->dialouts->attach_callerid_group($customer->id, $group->id, $dialout->id, $callerid_group->id);

        $cidgroups = $this->client->dialoutGroups->all($customer->id);

        $this->assertIsArray($cidgroups);
        $this->assertGreaterThan(0, count($cidgroups));

        $cidgroup = $cidgroups[0];

        $this->assertTrue($cidgroup->hasAttribute('id'));
        $this->assertTrue($cidgroup->hasAttribute('group_id'));
        $this->assertEquals($group->id, $cidgroup->group->id);
    }

    public function testAttachToDialoutCalleridGroup(): void
    {

        $customer = $this->customer;
        $group = $this->group;
        $callerid_group = $this->callerid_group;
        $dialout = $this->dialout;
        $initial_dgroups = $this->client->dialoutGroups->all($customer->id);

        $this->client->dialoutGroups->attach_callerid_group($customer->id, $group->id, $dialout->id, $callerid_group->id);

        $end_dgroups = $this->client->dialoutGroups->all($customer->id);
        $diff_dgroups = count($end_dgroups) - count($initial_dgroups);
        $this->assertEquals(1, $diff_dgroups);
    }

    public function testDetachDialoutCalleridGroup(): void
    {

        $customer = $this->customer;
        $group = $this->group;
        $callerid_group = $this->callerid_group;
        $dialout = $this->dialout;
        $initial_dgroups = $this->client->dialoutGroups->all($customer->id);

        $this->client->dialoutGroups->attach_callerid_group($customer->id, $group->id, $dialout->id, $callerid_group->id);
        $this->client->dialoutGroups->detach_callerid_group($customer->id, $group->id, $dialout->id, $callerid_group->id);

        $end_dgroups = $this->client->dialoutGroups->all($customer->id);
        $diff_dgroups = count($end_dgroups) - count($initial_dgroups);
        $this->assertEquals(0, $diff_dgroups);
    }

    public function testUpdateDialoutGroup(): void
    {
        $customer = $this->customer;
        $group = $this->group;
        $callerid_group = $this->callerid_group;
        $dialout = $this->dialout;
        $this->client->dialoutGroups->attach_callerid_group($customer->id, $group->id, $dialout->id, $callerid_group->id);

        $this->client->dialoutGroups->update($customer->id, $group->id, $dialout->id, $callerid_group->id, ['strip' => '666999', 'prepend' => '777999', 'minimal_call_duration_ms' => '1500']);

        $dgroups = $this->client->dialoutGroups->all($customer->id);
        $this->assertEquals('666999', $dgroups[0]->strip);
        $this->assertEquals('777999', $dgroups[0]->prepend);
        $this->assertEquals('1500', $dgroups[0]->minimal_call_duration_ms);
    }
}

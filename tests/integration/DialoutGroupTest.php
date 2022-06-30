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
}

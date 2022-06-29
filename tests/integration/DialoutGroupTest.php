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
    }

    public function testQueryAllDialoutGroup(): void
    {
        $customer = $this->customer;
        $group = $this->createDefaultGroup($customer->id);
        $callerid_group = $this->createDefaultCalleridGroup($customer->id);
        $dialout = $this->createDefaultDialout($customer->id);
        $this->client->dialouts->attach_callerid_group($customer->id, $group->id, $dialout->id, $callerid_group->id);

        $cidgroups = $this->client->dialoutGroups->all($customer->id);

        $this->assertIsArray($cidgroups);
        $this->assertGreaterThan(0, count($cidgroups));

        $cidgroup = $cidgroups[0];

        $this->assertTrue($cidgroup->hasAttribute('id'));
    }
}

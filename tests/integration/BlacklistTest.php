<?php

/**
 * Copyright 2022 Picallex Holding Group. All rights reserved.
 *
 * @author (2022) Jovany Leandro G.C <jovany@picallex.com>
 */

declare(strict_types=1);

require_once('ClientTestCase.php');

class BlacklistTest extends ClientTestCase
{
    protected function localSetUp(): void
    {
        $this->customer = $this->createDefaultCustomer();
    }

    public function testQueryAllBlacklist(): void
    {
        $this->markTestSkipped('need data in server');
        return;

        $lists = $this->client->blacklists->all($this->customer->id);

        $this->assertIsArray($lists);
        $this->assertGreaterThan(0, count($lists));

        $blacklist = $lists[0];
        $this->assertTrue($blacklist->hasAttribute('id'));
        $this->assertTrue($blacklist->hasAttribute('customer_id'));
        $this->assertTrue($blacklist->hasAttribute('number'));

        $customer = $this->client->preload($blacklist->customer);
        $this->assertEquals($customer->id, $this->customer->id);
    }
}

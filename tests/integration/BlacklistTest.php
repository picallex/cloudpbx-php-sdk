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

    public function testBlockNumber(): void
    {
        $blacklist = $this->client->blacklists->create($this->customer->id, '123456789');

        $this->assertEquals('123456789', $blacklist->number);
        $this->assertEquals($this->customer->id, $blacklist->customer_id);
    }

   public function testUnblockNumber(): void
    {
        $blacklist = $this->client->blacklists->create($this->customer->id, '1234567899');

        $this->assertNotThrowsException(function() use ($blacklist) {
            $this->client->blacklists->delete($this->customer->id, $blacklist->id);
        });
    }

    public function testQueryAllBlacklist(): void
    {
        $this->client->blacklists->create($this->customer->id, '1234567898');

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

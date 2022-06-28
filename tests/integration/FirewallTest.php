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


class FirewallTest extends ClientTestCase
{
    public function testAddIpToFirewall(): void
    {
        $cidr_block = "32.62.128.192/32";
        $status = 'whitelist';

        $ipset = $this->client->firewallIpSets->create($cidr_block, $status);

        $this->assertTrue($ipset->hasAttribute('cidr_block'));
        $this->assertTrue($ipset->hasAttribute('status'));

        $this->assertEquals($cidr_block, $ipset->cidr_block);
        $this->assertEquals($status, $ipset->status);
    }

    public function testQueryAllFirewallIpSet(): void
    {
        $this->client->firewallIpSets->create('1.2.3.4/32', 'whitelist');

        $ipsets = $this->client->firewallIpSets->all();
        $this->assertIsArray($ipsets);
        $this->assertGreaterThan(0, count($ipsets));


        $ipset = $ipsets[0];
        $this->assertTrue($ipset->hasAttribute('status'));
        $this->assertTrue($ipset->hasAttribute('cidr_block'));
    }

    public function testDeleteIpFromFirewall(): void
    {
        $cidr_block = '1.2.3.5/32';
        $this->client->firewallIpSets->create($cidr_block, 'whitelist');

        $this->client->firewallIpSets->delete($cidr_block);

        $this->assertTrue(true, 'expected not throw exception');
    }
}

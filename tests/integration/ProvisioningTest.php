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

class ProvisioningTest extends ClientTestCase
{
    public function testRunProvisioningForCustomer(): void
    {
        $customer = $this->createDefaultCustomer();

        $provisioning = $this->client->provisioning->run($customer->id);

        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\Provisioning::class, $provisioning);
        $this->assertTrue($provisioning->hasAttribute('customer_id'));
        $this->assertTrue($provisioning->hasAttribute('domain'));
        $this->assertTrue($provisioning->hasAttribute('switchname'));
        $this->assertEquals($customer->id, $provisioning->customer_id);
        $this->assertNotEmpty($provisioning->domain);
        $this->assertNotEmpty($provisioning->switchname);
    }
}

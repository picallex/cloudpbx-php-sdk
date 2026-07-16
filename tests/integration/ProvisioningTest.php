<?php

/**
 * Copyright 2022 Picallex Holding Group. All rights reserved.
 *
 * @author (2026) Agustin Serra <agustin@picallex.com>
 */

declare(strict_types=1);

require_once('ClientTestCase.php');
use PHPUnit\Framework\TestCase;
use Cloudpbx\Protocol;
use Cloudpbx\Util;

final class ProvisioningTest extends ClientTestCase
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

    public function testListAndShowProvisioningAttempts(): void
    {
        $customer = $this->createDefaultCustomer();
        $this->client->provisioning->run($customer->id);

        $attempts = $this->client->provisioning->attempts($customer->id);

        $this->assertIsArray($attempts);
        $this->assertNotEmpty($attempts);
        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\ProvisioningAttempt::class, $attempts[0]);
        $this->assertEquals($customer->id, $attempts[0]->customer_id);

        $attempt = $this->client->provisioning->attempt($customer->id, $attempts[0]->id);

        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\ProvisioningAttempt::class, $attempt);
        $this->assertEquals($attempts[0]->id, $attempt->id);
        $this->assertIsArray($attempt->steps);
        $this->assertNotEmpty($attempt->steps);
        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\ProvisioningAttemptStep::class, $attempt->steps[0]);
        $this->assertNotEmpty($attempt->steps[0]->name);
    }
}

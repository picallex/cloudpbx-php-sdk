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

class CustomerTest extends ClientTestCase
{
    protected function localSetUp(): void
    {
        $this->customer = $this->createDefaultCustomer();
    }

    public function testCreateCustomerWithMinimalData(): array
    {
        $domain = $this->generateRandomDomain();
        $name = $this->generateRandomString(5);

        $customer = $this->client->customers->create([
            'name' => $name,
            'domain' => $domain
        ]);

        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\Customer::class, $customer);
        $this->assertTrue($customer->hasAttribute('id'));
        $this->assertTrue($customer->id > 0);
        $this->assertEquals($name, $customer->name);
        $this->assertEquals($domain, $customer->domain);

        return [$customer];
    }

    /**
     * @depends testCreateCustomerWithMinimalData
     */
    public function testQueryAllCustomers(): array
    {
        $customers = $this->client->customers->all();
        $this->assertIsArray($customers);

        $customer = $customers[1];
        $this->assertTrue($customer->hasAttribute('id'));
        $this->assertTrue($customer->hasAttribute('name'));
        $this->assertTrue($customer->hasAttribute('domain'));

        return [$customer];
    }

   /**
     * @depends testCreateCustomerWithMinimalData
     */
    public function testQueryOneCustomer(array $stack): array
    {
        $customer = array_pop($stack);

        $customer = $this->client->customers->show($customer->id);

        $this->assertTrue($customer->hasAttribute('id'));
        $this->assertTrue($customer->hasAttribute('name'));

        return [$customer];
    }

    public function testUpdateCustomer(): void
    {
        $customer = $this->customer;

        $customer_updated = $this->client->customers->update($customer->id, [
            'limit_external_calls' => 66
        ]);

        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\Customer::class, $customer_updated);
        $this->assertEquals(66, $customer_updated->limit_external_calls);
        $this->assertEquals($customer->name, $customer_updated->name);
        $this->assertEquals($customer->domain, $customer_updated->domain);
    }

    public function testQueryCapabilitiesOfCustomer(): void
    {
        $customer = $this->customer;

        $caps = $this->client->customers->capabilities($customer->id);
        $this->assertIsArray($caps);
        $this->assertGreaterThan(1, count($caps));

        $cap = $caps[0];
        $this->assertTrue($cap->hasAttribute('id'));
        $this->assertTrue($cap->hasAttribute('customer_id'));
        $this->assertTrue($cap->hasAttribute('capability'));
        $this->assertTrue($cap->hasAttribute('allowed'));
    }

    public function testQueryChangeCapabilitiesOfCustomer(): void
    {
        $customer = $this->customer;

        $cap_enabled = $this->client->customers->enable_capability($customer->id, "inbound_calls");
        $cap_disabled = $this->client->customers->disable_capability($customer->id, "inbound_calls");

        $this->assertEquals($cap_enabled->id, $cap_disabled->id);
        $this->assertEquals($cap_enabled->allowed, true);
        $this->assertEquals($cap_disabled->allowed, false);
    }
}

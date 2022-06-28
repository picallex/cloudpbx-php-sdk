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


class AclIpv4Test extends ClientTestCase
{
    protected function localSetUp(): void
    {
        $this->customer = $this->createDefaultCustomer();
    }

    public function testAddAclIpv4toCustomer(): void
    {
        $customer = $this->customer;

        $acl = $this->client->aclIpv4s->create($customer->id, '192.168.0.0/24');
        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\AclIpv4::class, $acl);
        $this->assertEquals($customer->id, $acl->customer_id);
        $this->assertEquals('192.168.0.0/24', $acl->cidr);
    }

    public function testRemoveAclIpv4toCustomer(): void
    {
        $customer = $this->customer;

        $this->client->aclIpv4s->create($customer->id, '192.167.0.0/24');

        $this->client->aclIpv4s->delete($customer->id, '192.167.0.0/24');
        $this->assertTrue(true);
    }

    public function testRemoveAclIpv4IfNotExistsThrowsException(): void
    {
        $customer = $this->customer;

        $this->expectException(\Cloudpbx\Protocol\Error\NotFoundError::class);
        $this->client->aclIpv4s->delete($customer->id, '190.15.0.0/24');
    }

    public function testListAclIpv4toCustomer(): void
    {
        $customer = $this->customer;

        $this->client->aclIpv4s->create($customer->id, '192.163.0.0/24');

        $acls = $this->client->aclIpv4s->all($customer->id);
        $this->assertIsArray($acls);
        $this->assertGreaterThan(0, count($acls));
    }
}

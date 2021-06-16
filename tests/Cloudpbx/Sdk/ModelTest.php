<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Cloudpbx\Sdk\Model;

class ModelTest extends TestCase
{

    public function testEveryModelRequireId(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $customer = Model\Customer::fromArray(['name' => 'bob']);
    }

    public function testAccesingToFields(): void
    {
        $customer = Model\Customer::fromArray(['id' => 3, 'name' => 'bob']);
        $this->assertEquals('bob', $customer->name);
        $this->assertEquals(3, $customer->id);
    }
}

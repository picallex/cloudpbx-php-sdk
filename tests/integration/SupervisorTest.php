<?php

/**
 * Copyright 2022 Picallex Holding Group. All rights reserved.
 *
 * @author (2022) Jovany Leandro G.C <jovany@picallex.com>
 */

declare(strict_types=1);

require_once('ClientTestCase.php');

class SupervisorTest extends ClientTestCase
{
    protected function localSetUp(): void
    {
        $this->customer = $this->createDefaultCustomer();
    }

    public function testCreateSupervisor(): void
    {
        $user = $this->createDefaultUser($this->customer->id);

        $supervisor = $this->client->supervisors->create($this->customer->id, $user->id, '1234');

        $this->assertEquals($user->id, $supervisor->user_id);
        $this->assertEquals('1234', $supervisor->spy_authentication_code);
    }

    public function testDeleteSupervisor(): void
    {
        $user = $this->createDefaultUser($this->customer->id);
        $supervisor = $this->client->supervisors->create($this->customer->id, $user->id, '1234');

        $this->client->supervisors->delete($this->customer->id, $user->id);

        $this->assertTrue(true);
    }

    public function testDeleteSupervisorErrorIfNotExists(): void
    {
        $user = $this->createDefaultUser($this->customer->id);
        $supervisor = $this->client->supervisors->create($this->customer->id, $user->id, '1234');

        $this->assertThrowsException(\Cloudpbx\Protocol\Error\NotFoundError::class, function ()  {
            $this->client->supervisors->delete($this->customer->id, 99);
        });
    }

    public function testQueryAllSupervisor(): void
    {
        $user = $this->createDefaultUser($this->customer->id);
        $this->client->supervisors->create($this->customer->id, $user->id, '1234');


        $supervisors = $this->client->supervisors->all($this->customer->id);

        $this->assertIsArray($supervisors);
        $this->assertGreaterThan(0, count($supervisors));

        $supervisor = $supervisors[0];
        $this->assertTrue($supervisor->hasAttribute('id'));
        $this->assertTrue($supervisor->hasAttribute('customer_id'));
        $this->assertTrue($supervisor->hasAttribute('user_id'));
    }


}

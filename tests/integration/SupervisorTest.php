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
    public function testQueryAllSupervisor(): void
    {
        $this->markTestSkipped('need data in server');
        return;

        $supervisors = $this->client->supervisors->all(self::$customer_id);

        $this->assertIsArray($supervisors);
        $this->assertGreaterThan(0, count($supervisors));

        $supervisor = $supervisors[0];
        $this->assertTrue($supervisor->hasAttribute('id'));
        $this->assertTrue($supervisor->hasAttribute('customer_id'));
        $this->assertTrue($supervisor->hasAttribute('user_id'));
    }
}

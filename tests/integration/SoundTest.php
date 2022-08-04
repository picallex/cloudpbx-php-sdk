<?php

/**
 * Copyright 2022 Picallex Holding Group. All rights reserved.
 *
 * @author (2022) Jovany Leandro G.C <jovany@picallex.com>
 */

declare(strict_types=1);

require_once('ClientTestCase.php');

class SoundTest extends ClientTestCase
{
    protected function localSetUp(): void
    {
        $this->customer = $this->createDefaultCustomer();
    }

    public function testQueryAllSound(): void
    {
        $this->markTestSkipped('need data in server');
        return;

        $sounds = $this->client->sounds->all(self::$customer_id);

        $this->assertIsArray($sounds);
        $this->assertGreaterThan(0, count($sounds));

        $sound = $sounds[0];
        $this->assertTrue($sound->hasAttribute('id'));
        $this->assertTrue($sound->hasAttribute('customer_id'));
        $this->assertTrue($sound->hasAttribute('name'));
        $this->assertTrue($sound->hasAttribute('template'));
        $this->assertTrue($sound->hasAttribute('usage'));

        $new_sound = $this->client->sounds->show(self::$customer_id, $sound->id);
        $this->assertEquals($new_sound->id, $sound->id);
        $this->assertEquals($new_sound->customer_id, $sound->customer_id);
    }

    public function testCreateSound(): void
    {
        $entry = $this->client->sounds->create($this->customer->id, 'audio', 'default', 'ivr_exit', 'tests/integration/example.ogg');

        $this->assertTrue($entry->hasAttribute('id'));
        $this->assertEquals('ivr_exit', $entry->usage);
        $this->assertEquals('default', $entry->template);
        $this->assertEquals('audio', $entry->name);
    }
}

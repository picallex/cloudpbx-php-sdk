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

class IvrMenuTest extends ClientTestCase
{
    protected function localSetUp(): void
    {
        $this->customer = $this->createDefaultCustomer([]);
    }

    public function testCreateIvrMenu(): void
    {
        $customer = $this->customer;

        $ivr = $this->client->ivrMenus->create($customer->id, [
            'name' => 'test-ivr-mune',
            'description' => 'test ivr menu'
        ]);

        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\IvrMenu::class, $ivr);
        $this->assertTrue($ivr->id > 0);
        $this->assertEquals('test-ivr-mune', $ivr->name);
        $this->assertEquals('test ivr menu', $ivr->description);
    }

    public function testQueryAllIvrMenu(): void
    {
        $this->createDefaultIvrMenu($this->customer->id);

        $ivrmenus = $this->client->ivrMenus->all($this->customer->id);

        $this->assertIsArray($ivrmenus);
        $this->assertGreaterThan(0, count($ivrmenus));

        $ivrmenu = $ivrmenus[0];
        $this->assertTrue($ivrmenu->hasAttribute('id'));
        $this->assertTrue($ivrmenu->hasAttribute('name'));
        $this->assertEquals($this->customer->id, $ivrmenu->customer->id);
    }

    public function testDeleteIvrMenu(): void
    {
        $customer = $this->customer;
        $ivr = $this->createDefaultIvrMenu($customer->id);

        $this->client->ivrMenus->delete($customer->id, $ivr->id);

        try {
            $this->client->ivrMenus->show($customer->id, $ivr->id);
        } catch(Exception $e) {
            $this->assertInstanceOf(\Cloudpbx\Protocol\Error\NotFoundError::class, $e);
        }
    }

    public function testQueryAllIvrMenuEntries(): void
    {
        $this->markTestIncomplete(
            'This test not have staging environment'
        );
        return;

        $customer = $this->customer;
        $ivrmenu = /** not implemented */

        $entries = $this->client->ivrMenuEntries->all(self::$customer_id, $ivrmenu->id);

        $this->assertIsArray($entries);
        $this->assertGreaterThan(0, count($entries));

        $entry = $entries[0];
        $this->assertTrue($entry->hasAttribute('id'));
        $this->assertTrue($entry->hasAttribute('digits'));
        $this->assertTrue($entry->hasAttribute('param'));
        $this->assertTrue($entry->hasAttribute('action'));
    }
}

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


class FollowMeTest extends ClientTestCase
{
    protected function localSetUp(): void
    {
        $this->customer = $this->createDefaultCustomer();
    }


    public function testCreateFollowMe(): void
    {
        $customer = $this->customer;

        $name = $this->generateRandomString(5);
        $me = $this->client->followMes->create($customer->id, [
            'name' => $name,
            'ringback_type' => 'fake_ring', //bridge_media
        ]);

        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\FollowMe::class, $me);
        $this->assertTrue($me->id > 0);
        $this->assertEquals($name, $me->name);
        $this->assertEquals($customer->id, $me->customer_id);
    }

    public function testDeleteFollowMe(): void
    {
        $customer = $this->customer;
        $me = $this->createDefaultFollowMe($customer->id);

        $this->client->followMes->delete($customer->id, $me->id);

        try {
            $this->client->followMes->show($customer->id, $me->id);
        } catch(Exception $e) {
            $this->assertInstanceOf(\Cloudpbx\Protocol\Error\NotFoundError::class, $e);
        }
    }

    public function testQueryAllFollowMe(): array
    {
        $customer = $this->customer;
        $me = $this->createDefaultFollowMe($customer->id);

        $follows = $this->client->followMes->all($customer->id);

        $this->assertIsArray($follows);
        $this->assertGreaterThan(0, count($follows));

        $follow = $follows[0];
        $this->assertTrue($follow->hasAttribute('id'));
        $this->assertTrue($follow->hasAttribute('customer_id'));
        $this->assertTrue($follow->hasAttribute('name'));

        return [$follow];
    }

    public function testCreateFollowMeEntryTypeCallcenterQueue(): void
    {
        $customer = $this->customer;
        $me = $this->createDefaultFollowMe($customer->id);
        $queue = $this->createDefaultCallcenterQueue($customer->id);

        $entry = $this->client->followMeEntries->create_callcenter_queue($customer->id, $me->id, $queue->id,
                                                                           ['priority' => 100]);

        $this->assertTrue($entry->hasAttribute('id'));
        $this->assertTrue($entry->hasAttribute('follow_me_id'));
        $this->assertEquals($queue->id, $entry->callcenter_queue_id);
        $this->assertEquals($entry->priority, 100);
    }

    public function testCreateFollowMeEntryTypeUser(): void
    {
        $customer = $this->customer;
        $user = $this->createDefaultUser($customer->id);
        $me = $this->createDefaultFollowMe($customer->id);

        $entry = $this->client->followMeEntries->create_user($customer->id, $me->id, $user->id, ['priority' => 100]);

        $this->assertTrue($entry->hasAttribute('id'));
        $this->assertTrue($entry->hasAttribute('follow_me_id'));
        $this->assertEquals($user->id, $entry->user_id);
        $this->assertEquals($entry->priority, 100);

        $relation = $this->client->preload($entry->belongs_to);
        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\User::class, $relation);
        $this->assertEquals($user->id, $relation->id);
    }

    public function testCreateFollowMeEntryTypeSound(): void
    {
        $customer = $this->customer;
        $sound = $this->createDefaultSound($customer->id, 'ivr_exit');
        $me = $this->createDefaultFollowMe($customer->id);

        $entry = $this->client->followMeEntries->create_sound($customer->id, $me->id, $sound->id, ['priority' => 100]);

        $this->assertTrue($entry->hasAttribute('id'));
        $this->assertTrue($entry->hasAttribute('follow_me_id'));
        $this->assertEquals($sound->id, $entry->sound_id);
        $this->assertEquals($entry->priority, 100);
    }

    public function testCreateFollowMeEntryTypeDialout(): void
    {
        $customer = $this->customer;
        $me = $this->createDefaultFollowMe($customer->id);
        $dialout = $this->createDefaultDialout($customer->id);

        $entry = $this->client->followMeEntries->create_dialout($customer->id, $me->id, $dialout->id, '999666555', ['priority' => 100, 'call_timeout' => 60]);

        $this->assertTrue($entry->hasAttribute('id'));
        $this->assertTrue($entry->hasAttribute('follow_me_id'));
        $this->assertEquals(60, $entry->call_timeout);
        $this->assertEquals(100, $entry->priority);
        $this->assertEquals('999666555', $entry->dialout_number);

        $relation = $this->client->preload($entry->belongs_to);
        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\Dialout::class, $relation);
        $this->assertEquals($dialout->id, $relation->id);
    }

    public function testCreateFollowMeEntryTypeDialoutWithMultipleNumbers(): void
    {
        $customer = $this->customer;
        $me = $this->createDefaultFollowMe($customer->id);
        $dialout = $this->createDefaultDialout($customer->id);

        $entry = $this->client->followMeEntries->create_dialout($customer->id, $me->id, $dialout->id, '999666555,999555666', ['priority' => 100, 'call_timeout' => 60]);

        $this->assertTrue($entry->hasAttribute('id'));
        $this->assertTrue($entry->hasAttribute('follow_me_id'));
        $this->assertEquals(60, $entry->call_timeout);
        $this->assertEquals(100, $entry->priority);
        $this->assertEquals('999666555,999555666', $entry->dialout_number);

        $relation = $this->client->preload($entry->belongs_to);
        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\Dialout::class, $relation);
        $this->assertEquals($dialout->id, $relation->id);
    }

    public function testCreateFollowMeEntryTypeIvrMenu(): void
    {
        $customer = $this->customer;
        $me = $this->createDefaultFollowMe($customer->id);
        $ivrmenu = $this->createDefaultIvrMenu($customer->id);

        $entry = $this->client->followMeEntries->create_ivr_menu($customer->id, $me->id, $ivrmenu->id, ['priority' => 100]);

        $this->assertTrue($entry->hasAttribute('id'));
        $this->assertTrue($entry->hasAttribute('follow_me_id'));
        $this->assertEquals($ivrmenu->id, $entry->ivr_menu_id);
        $this->assertEquals($entry->priority, 100);

        $relation = $this->client->preload($entry->belongs_to);
        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\IvrMenu::class, $relation);
        $this->assertEquals($ivrmenu->id, $relation->id);
    }


    public function testQueryAllFollowMeEntries(): void
    {
        $customer = $this->customer;
        $me = $this->createDefaultFollowMe($customer->id);
        $user = $this->createDefaultUser($customer->id);
        $this->client->followMeEntries->create_user($customer->id, $me->id, $user->id, ['priority' => 100]);

        $entries = $this->client->followMeEntries->all($customer->id, $me->id);

        $this->assertIsArray($entries);
        $this->assertGreaterThan(0, count($entries));

        $entry = $entries[0];
        $this->assertTrue($entry->hasAttribute('id'));
        $this->assertTrue($entry->hasAttribute('follow_me_id'));
    }

    public function testCreateFollowMeEntryTypeVoicemail(): void
    {
        $customer = $this->customer;
        $user = $this->createDefaultUser($customer->id);
        $voicemail = $this->createDefaultVoicemail($customer->id, $user->id);
        $me = $this->createDefaultFollowMe($customer->id);

        $entry = $this->client->followMeEntries->create_voicemail($customer->id, $me->id, $voicemail->user_id, ['priority' => 100]);

        $this->assertTrue($entry->hasAttribute('id'));
        $this->assertTrue($entry->hasAttribute('follow_me_id'));
        $this->assertEquals($voicemail->id, $entry->voicemail_id);
        $this->assertEquals($entry->priority, 100);
    }

    public function testCreateFollowMeEntryTypeRedirectl(): void
    {
        $customer = $this->customer;
        $source = $this->client->followMes->create($customer->id, [
            'name' => $this->generateRandomString(5),
        ]);
        $destination = $this->client->followMes->create($customer->id, [
            'name' => $this->generateRandomString(5),
            'ringback_type' => 'fake_ring',
        ]);

        $entry = $this->client->followMeEntries->create_redirect($customer->id, $source->id, $destination->id, ['priority' => 100]);

        $this->assertTrue($entry->hasAttribute('id'));
        $this->assertTrue($entry->hasAttribute('follow_me_id'));
        $this->assertEquals($entry->priority, 100);
        $this->assertEquals($source->id, $entry->follow_me_id);
        $this->assertEquals($destination->id, $entry->redirect_follow_me_id);
        // check cast of relation
        $redirect = $this->client->preload($entry->belongs_to);
        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\FollowMe::class, $redirect);
        $this->assertEquals($destination->id, $redirect->id);
    }

    public function testCreateFollowMeEntryOnlyIfMatchConditions(): void
    {
        $customer = $this->customer;
        $source = $this->client->followMes->create($customer->id, [
            'name' => $this->generateRandomString(5),
        ]);
        $destination = $this->client->followMes->create($customer->id, [
            'name' => $this->generateRandomString(5),
            'ringback_type' => 'fake_ring',
        ]);

        $only_if_match_conditions = 'inbound_caller_id_number::^[^\\d]+$';
        $entry = $this->client->followMeEntries->create_redirect($customer->id, $source->id, $destination->id, [
            'priority' => 100, 'only_if_match_conditions' => $only_if_match_conditions
        ]);

        $this->assertEquals('inbound_caller_id_number::^[^\\d]+$', $entry->only_if_match_conditions);
    }

    public function testUpdateFollowMeEntry(): void
    {
        $customer = $this->customer;
        $user = $this->createDefaultUser($customer->id);
        $me = $this->createDefaultFollowMe($customer->id);
        $entry = $this->client->followMeEntries->create_user($customer->id, $me->id, $user->id, ['priority' => 100]);

        $entry_updated = $this->client->followMeEntries->update($customer->id, $me->id, $entry->id, ['priority' => 999]);

        $this->assertEquals($entry->id, $entry_updated->id);
        $this->assertEquals(999, $entry_updated->priority);
    }

    public function testDeleteFollowMeEntry(): void
    {
        $customer = $this->customer;
        $user = $this->createDefaultUser($customer->id);
        $me = $this->createDefaultFollowMe($customer->id);
        $entry = $this->client->followMeEntries->create_user($customer->id, $me->id, $user->id, ['priority' => 100]);


        $this->client->followMeEntries->delete($customer->id, $me->id, $entry->id);
        try {
            $this->client->followmeEntries->show($customer->id, $me->id, $entry->id);
            $this->fail("not show");
        } catch (Exception $e) {
            $this->assertInstanceOf(\Cloudpbx\Protocol\Error\NotFoundError::class, $e);
        }

    }

    public function testCreateAndUpdateFollowMe(): void
    {
        $customer = $this->customer;
        $name = $this->generateRandomString(5);

        $me = $this->client->followMes->create($customer->id, [
            'name' => $name,
            'ringback_type' => 'fake_ring',
        ]);

        $me = $this->client->followMes->update($customer->id, $me->id, [
            'name' => '1999',
        ]);

        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\FollowMe::class, $me);
        $this->assertTrue($me->id > 0);
        $this->assertEquals('1999', $me->name);
        $this->assertEquals($customer->id, $me->customer_id);
    }
}

<?php

/**
 * Copyright 2024 Picallex Holding Group. All rights reserved.
 *
 * @author (2022) Jovany Leandro G.C <jovany@picallex.com>
 * @author (2024) Matias Damian Gomez <matias@picallex.com>
 */

declare(strict_types=1);

require_once('ClientTestCase.php');
use PHPUnit\Framework\TestCase;
use Cloudpbx\Protocol;
use Cloudpbx\Util;


class UserTest extends ClientTestCase
{
    protected function localSetUp(): void
    {
        $this->customer = $this->createDefaultCustomer();
    }

    public function testCreateUserWithMinimalData(): int
    {
        $customer_id = $this->customer->id;
        $user = $this->client->users->create($customer_id, [
            'name' => '234',
            'password' => $this->generateUserPassword(),
            'is_webrtc' => false
        ]);

        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\User::class, $user);
        $this->assertTrue($user->id > 0);
        $this->assertEquals('234', $user->name);
        $this->assertEquals(false, $user->is_webrtc);

        return $user->id;
    }

    public function testCreateUserWithFullData(): void
    {
        $customer_id = $this->customer->id;
        $user = $this->client->users->create($customer_id, [
            'name' => '2312',
            'password' => $this->generateUserPassword(),
            'alias' => 'description',
            'accountcode' => 'invoice',
            'caller_name' => 'caller-name',
            'caller_number' => 'caller-number',
            'dnd_on_sip_unregister' => true,
            'available_on_sip_register' => true,
            'is_webrtc' => true,
            'enable_outbound_fakering' => true
        ]);

        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\User::class, $user);
        $this->assertTrue($user->id > 0);
        $this->assertEquals('2312', $user->name);
        $this->assertEquals(true, $user->is_webrtc);
        $this->assertEquals('description', $user->alias);
        $this->assertEquals('invoice', $user->accountcode);
        $this->assertEquals('caller-name', $user->caller_name);
        $this->assertEquals('caller-number', $user->caller_number);
        $this->assertEquals(true, $user->dnd_on_sip_unregister);
        $this->assertEquals(true, $user->available_on_sip_register);
        $this->assertEquals(true, $user->enable_outbound_fakering);
    }

    public function testQueryAllUsers(): void
    {
        $last_customer_id = $this->customer->id;
        $this->createDefaultUser($last_customer_id);

        $users = $this->client->users->all($last_customer_id);
        $this->assertIsArray($users);
        $this->assertGreaterThan(0, count($users));

        $user = $users[0];
        $this->assertTrue($user->hasAttribute('id'));
        $this->assertTrue($user->hasAttribute('caller_name'));
        $this->assertTrue($user->hasAttribute('caller_number'));
        $this->assertTrue($user->hasAttribute('accountcode'));
        $this->assertTrue($user->hasAttribute('alias'));
        $this->assertTrue($user->hasAttribute('do_not_disturb'));
    }

    public function testQueryOneUser(): void
    {
        $last_user_id = $this->createDefaultUser($this->customer->id)->id;
        $customer_id = $this->customer->id;

        $user = $this->client->users->show($customer_id, $last_user_id);

        $this->assertEquals($user->id, $last_user_id);
    }

    public function testUpdateUser(): void
    {
        $customer_id =$this->customer->id;
        $user_id = $this->createDefaultUser($customer_id)->id;

        $user_updated = $this->client->users->update($customer_id, $user_id, [

            'alias' => 'new alias'
        ]);

        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\User::class, $user_updated);
        $this->assertEquals($user_id, $user_updated->id);
        $this->assertEquals('new alias', $user_updated->alias);
    }

    public function testDeleteUser(): void
    {
        $customer_id =$this->customer->id;
        $user = $this->createDefaultUser($customer_id);

        $this->client->users->delete($user->customer_id, $user->id);

        try {
            $this->client->users->show($user->customer_id, $user->id);
        } catch(Exception $e) {
            $this->assertInstanceOf(\Cloudpbx\Protocol\Error\NotFoundError::class, $e);
        }
    }
}

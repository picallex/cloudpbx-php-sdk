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


class GroupTest extends ClientTestCase
{
    protected function localSetUp(): void
    {
        $this->customer = $this->createDefaultCustomer();
    }

    public function testCreateGroup(): void
    {
        $customer = $this->customer;
        $name = $this->generateRandomName();
        $alias = $this->generateRandomName();
        $group = $this->client->groups->create($customer->id, [
            'name' => $name,
            'alias' => $alias
        ]);

        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\Group::class, $group);
        $this->assertTrue($group->id > 0);
        $this->assertEquals($name, $group->name);
        $this->assertEquals($alias, $group->alias);
    }

    public function testUpdateGroup(): void
    {
        $customer = $this->customer;
        $group = $this->createDefaultGroup($customer->id);

        $new_name = $this->generateRandomName();
        $new_alias = $this->generateRandomName();
        $group_updated = $this->client->groups->update($group->customer_id, $group->id, [
            'name' => $new_name,
            'alias' => $new_alias
        ]);

        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\Group::class, $group);
        $this->assertEquals($group->id, $group_updated->id);
        $this->assertEquals($new_name, $group_updated->name);
        $this->assertEquals($new_alias, $group_updated->alias);
    }


    public function testDeleteGroup(): void
    {
        $customer = $this->customer;
        $group = $this->createDefaultGroup($customer->id);

        $this->client->groups->delete($customer->id, $group->id);

        try {
            $this->client->groups->show($customer->id, $group->id);
        } catch(Exception $e) {
            $this->assertInstanceOf(\Cloudpbx\Protocol\Error\NotFoundError::class, $e);
        }
    }

    public function testListGroup(): void
    {
        $customer = $this->customer;
        $group = $this->createDefaultGroup($customer->id);

        $records = $this->client->groups->all($customer->id);
        $this->assertTrue(count($records) > 0);


        $record = $records[0];
        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\Group::class, $record);
    }

    public function testListUsersOfGroup(): void
    {
        $customer = $this->customer;
        $user = $this->createDefaultUser($customer->id);
        $group = $this->createDefaultGroup($customer->id);

        $this->client->groups->attach_user($customer->id, $group->id, $user->id);

        $group_with_users = $this->client->groups->show($customer->id, $group->id);

        $users = $group_with_users->users_relation;
        $user_relation = $this->client->preload($users[0]);
        $this->assertEquals($user->id, $user_relation->id);
    }

    public function testListGroupsOfUser(): void
    {
        $customer = $this->customer;
        $user = $this->createDefaultUser($customer->id);
        $group = $this->createDefaultGroup($customer->id);
        $group2 = $this->createDefaultGroup($customer->id);

        $this->client->groups->attach_user($customer->id, $group->id, $user->id);
        $this->client->groups->attach_user($customer->id, $group2->id, $user->id);

        $groups = $this->client->groups->findByUser($customer->id, $user->id);

        $this->assertEquals($group->name, $groups[0]->name);
        $this->assertEquals($group2->name, $groups[1]->name);
    }

    public function testAttachDetachUserToOrFromGroup(): void
    {
        $customer = $this->customer;
        $user = $this->createDefaultUser($customer->id);
        $group = $this->createDefaultGroup($customer->id);

        $this->client->groups->attach_user($customer->id, $group->id, $user->id);

        $this->client->groups->detach_user($customer->id, $group->id, $user->id);

        try {
            $this->client->groups->detach_user($customer->id, $group->id, $user->id);
        } catch(Exception $e) {
            $this->assertInstanceOf(\Cloudpbx\Protocol\Error\NotFoundError::class, $e);
        }
        $this->assertTrue(true);
    }
}

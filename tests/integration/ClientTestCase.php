<?php

/**
 * Copyright 2022 Picallex Holding Group. All rights reserved.
 *
 * @author (2022) Jovany Leandro G.C <jovany@picallex.com>
 */

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Cloudpbx\Protocol;
use Cloudpbx\Util;

class ClientTestCase extends TestCase
{
    protected function setUp(): void
    {
        $base = Util\Environment::get('test', 'cloudpbx_api_base');
        $api_key = Util\Environment::get('test', 'cloudpbx_api_key');
        $this->client = \Cloudpbx\Sdk::createDefaultClient($base, $api_key);
        static::localSetUp();
    }

    protected function localSetUp(): void
    {
    }

    protected function createDefaultCustomer($params = []) {
        $nparams = array_merge([
            'name' => $this->generateRandomString(5),
            'domain' => $this->generateRandomDomain()
        ], $params);
        return $this->client->customers->create($nparams);
    }

    protected function createDefaultUser($customer_id, $params = []) {
        $nparams = array_merge([
                                   'name' => $this->generateRandomString(5),
                                   'password' => $this->generateUserPassword(),
                                   'alias' => 'description',
                                   'accountcode' => 'invoice',
                                   'caller_name' => 'caller-name',
                                   'caller_number' => 'caller-number',
                                   'dnd_on_sip_unregister' => true,
                                   'available_on_sip_register' => true,
                                   'is_webrtc' => true
        ], $params);
        return $this->client->users->create($customer_id, $nparams);
    }

    protected function createDefaultGroup($customer_id, $params = [])
    {
        $nparams = array_merge(['name' => $this->generateRandomName(),
                                'alias' => $this->generateRandomName()], $params);
        return $this->client->groups->create($customer_id, $nparams);
    }

    protected function createDefaultDialout($customer_id, $params = []) {
        $nparams = array_merge([
                                   'weight' => 100,
                                   'strip' => '9999',
                                   'prepend' => '88888',
                                   'name' => 'international',
                                   'destination' => $this->generateRandomNumber(10),
                                   'gateway_strategy' => 'sequence',
                                   'callerid_strategy' => 'random'
        ], $params);
        return $this->client->dialouts->create($customer_id, $nparams);
    }

    protected function createDefaultCallcenterQueue($customer_id, $params = []) {
        $nparams = array_merge([
            'name' => $this->generateRandomString(5),
            'strategy' => 'random', //round-robin, ring-all
            'max_wait_time' => 5,
            'description' => 'a super queue',
            'alias' => $this->generateRandomString(5)
        ], $params);
        return $this->client->callcenterQueues->create($customer_id, $nparams);
    }

    protected function createDefaultIvrMenu($customer_id, $params = []) {
        $nparams = array_merge(['name' => $this->generateRandomString(5)], $params);
        return $this->client->ivrMenus->create($customer_id, $nparams);
    }

    protected function createDefaultCalleridGroup($customer_id, $params = [])
    {
        $nparams = array_merge(['name' => $this->generateRandomName()], $params);
        return $this->client->calleridGroups->create($customer_id, $nparams);
    }

    protected function createDefaultVoicemail($customer_id, $user_id, $params = [])
    {
        return $this->client->voicemails->create($customer_id,
                                                 $user_id,
                                                 $this->generateRandomString(10),
                                                 'domain@'.$this->generateRandomDomain(),
                                                 [
                                                     'skip_greeting' => false,
                                                     'password' => '123'
                                                 ]);

    }

    protected function createDefaultSound($customer_id, $usage)
    {
        return $this->client->sounds->create($customer_id,
                                             $this->generateRandomName(),
                                             'default',
                                             $usage,
                                             'tests/integration/example.ogg');
    }

    protected function generateRandomName(): string
    {
        return $this->generateRandomString(10);
    }

    protected function generateRandomNumber($size): string
    {
        return $this->randomString(range(0, 100), $size);
    }

    protected function generateRandomDomain(): string
    {
        $topLevel = ['org', 'co', 'ar'];
        $domain = $this->randomString(range(0, 99), 30)
                . '.'
                . $this->randomString($topLevel, 1);

        return $domain;
    }

    protected function generateUserPassword(): string
    {
        $upper = ['A', 'C', 'Z', 'D', 'H'];
        $lower = ['b', 'c', 'e', 'f', 'g'];
        $numbers = [1, 2, 3, 4, 5, 6];
        $special = ['/', '@', '}', '*', '.'];

        $password = $this->randomString($upper, 2)
                  . $this->randomString($lower, 2)
                  . $this->randomString($numbers, 2)
                  . $this->randomString($special, 4);
        return $password;
    }

    protected function generateRandomString($size) {
        return $this->randomString(range(40, 90), $size);
    }

    private function randomString($seeds, $nitems) {
        $values = array_flip($seeds);
        if ($nitems == 1) {
            return array_rand($values, 1);
        }

        $coll = array_rand($values, $nitems);
        return implode('', $coll);
    }

    protected function assertThrowsException(string $klass, callable $fun): void
    {
        try {
            $fun();
        } catch (\Throwable $ex) {
            $this->assertEquals($klass, get_class($ex));
            return;
        }

        $this->assertTrue(false, 'not throws exception wants: ' . $klass);
    }
}

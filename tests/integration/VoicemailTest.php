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

class VoicemailTest extends ClientTestCase
{
    protected function localSetUp(): void
    {
        $this->customer = $this->createDefaultCustomer();
    }

    public function testQueryAllVoicemail(): void
    {
        $this->markTestSkipped('need data in server');
        return;

        $voicemails = $this->client->voicemails->all($this->customer->id);

        $this->assertIsArray($voicemails);
        $this->assertGreaterThan(0, count($voicemails));

        $voicemail = $voicemails[0];
        $this->assertTrue($voicemail->hasAttribute('id'));
        $this->assertTrue($voicemail->hasAttribute('user_id'));
    }


    public function testQueryOneVoicemail(): void
    {
        $this->markTestSkipped('need data in server');
        return;

        $entry = $this->client->voicemails->show($this->customer->id, $voicemail->id);

        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\Voicemail::class, $entry);
        $this->assertEquals($voicemail->id, $entry->id);
    }

    public function testCreateVoicemail(): void
    {
        $user = $this->createDefaultUser($this->customer->id);
        $entry = $this->client->voicemails->create($this->customer->id,
                                                   $user->id,
                                                   'voicemail test',
                                                   'voicemail@test.org',
                                                   [
                                                       'skip_greeting' => false,
                                                       'password' => '123'
                                                   ]);

        $this->assertTrue($entry->hasAttribute('id'));
        $this->assertEquals(false, $entry->skip_greeting);
        $this->assertEquals('123', $entry->password);
        $this->assertEquals('voicemail@test.org', $entry->mailto);
    }
}

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
        $user = $this->createDefaultUser($this->customer->id);
        $this->createDefaultVoicemail($this->customer->id, $user->id);

        $voicemails = $this->client->voicemails->all($this->customer->id);

        $this->assertIsArray($voicemails);
        $this->assertGreaterThan(0, count($voicemails));

        $voicemail = $voicemails[0];
        $this->assertTrue($voicemail->hasAttribute('id'));
        $this->assertTrue($voicemail->hasAttribute('user_id'));
    }


    public function testQueryOneVoicemail(): void
    {
        $user = $this->createDefaultUser($this->customer->id);
        $voicemail = $this->createDefaultVoicemail($this->customer->id, $user->id);

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

    public function testUpdateVoicemail(): void
    {
        $user = $this->createDefaultUser($this->customer->id);
        $voicemail = $this->createDefaultVoicemail($this->customer->id, $user->id);
        $updated_voicemail = $this->client->voicemails->update($this->customer->id, $user->id, $voicemail->id, [
            'mailto' => 'bob@marley.org',
            'password' => '123499'
        ]);

        $this->assertEquals($voicemail->id, $updated_voicemail->id);
        $this->assertEquals('123499', $updated_voicemail->password);
        $this->assertEquals('bob@marley.org', $updated_voicemail->mailto);
    }

    public function testDeleteVoicemail(): void
    {
        $user = $this->createDefaultUser($this->customer->id);
        $voicemail = $this->createDefaultVoicemail($this->customer->id, $user->id);
        $this->assertNotThrowsException(function () use ($voicemail) {
            $this->client->voicemails->delete($voicemail->customer_id, $voicemail->user_id, $voicemail->id);
        });
    }
}

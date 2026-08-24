<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Cloudpbx\Protocol\ProtocolHTTP;
use Cloudpbx\Sdk\Implementation\Client;
use Cloudpbx\Protocol\Http\Request;
use Cloudpbx\Protocol\Http\Response;
use Cloudpbx\Protocol\Http\Implementation\ResponseFromArray;

class CalleridReputationTest extends TestCase
{
    /**
     * fake transport that records the last request and returns a canned body.
     *
     * @param string $body
     * @param int $status_code
     * @return object
     */
    private function fakeTransport($body, $status_code = 200)
    {
        return new class ($body, $status_code) implements \Cloudpbx\Protocol\Http\Client {
            /** @var string */
            public $last_url;
            /** @var string */
            public $last_method;
            /** @var string|null */
            public $last_body;
            /** @var string */
            private $body;
            /** @var int */
            private $status_code;

            public function __construct($body, $status_code)
            {
                $this->body = $body;
                $this->status_code = $status_code;
            }

            public function sendRequest(Request $request): Response
            {
                $this->last_url = $request->url();
                $this->last_method = $request->method();
                $this->last_body = $request->body();
                return new ResponseFromArray($this->body, $this->status_code);
            }
        };
    }

    /**
     * @param object $transport
     * @return Client
     */
    private function clientWith($transport)
    {
        return new Client(new ProtocolHTTP('https://api.example.com', 'KEY', $transport));
    }

    /**
     * @return string
     */
    private function settingsBody()
    {
        return json_encode(['data' => [
            'enabled' => true,
            'batch_size' => 150,
            'pause_between_batches_ms' => 1000,
            'scheduler_enabled' => true,
            'run_at_utc_hour' => 6
        ]]);
    }

    public function testSettingsMapsTypedFields(): void
    {
        $transport = $this->fakeTransport($this->settingsBody());
        $client = $this->clientWith($transport);

        $settings = $client->calleridReputation->settings();

        $this->assertEquals('GET', $transport->last_method);
        $this->assertEquals(
            'https://api.example.com/api/v1/management/callerid_reputation/settings',
            $transport->last_url
        );
        $this->assertInstanceOf(\Cloudpbx\Sdk\Model\CalleridReputationSetting::class, $settings);
        $this->assertTrue($settings->enabled);
        $this->assertEquals(150, $settings->batch_size);
        $this->assertEquals(1000, $settings->pause_between_batches_ms);
        $this->assertTrue($settings->scheduler_enabled);
        $this->assertEquals(6, $settings->run_at_utc_hour);
    }

    public function testUpdateSettingsSendsFieldsAtTheRoot(): void
    {
        $transport = $this->fakeTransport($this->settingsBody());
        $client = $this->clientWith($transport);

        $client->calleridReputation->updateSettings(['enabled' => true, 'batch_size' => 100]);

        $this->assertEquals('PUT', $transport->last_method);
        // sin wrapper por recurso: el controller hace Map.take en la raiz
        $this->assertEquals('{"enabled":true,"batch_size":100}', $transport->last_body);
    }

    public function testSyncPostsCustomerIdAndReturnsTheAck(): void
    {
        $transport = $this->fakeTransport(
            json_encode(['data' => ['status' => 'accepted', 'customer_id' => 1387]]),
            202
        );
        $client = $this->clientWith($transport);

        $ack = $client->calleridReputation->sync(1387);

        $this->assertEquals('POST', $transport->last_method);
        $this->assertEquals(
            'https://api.example.com/api/v1/management/callerid_reputation/sync',
            $transport->last_url
        );
        $this->assertEquals('{"customer_id":1387}', $transport->last_body);
        $this->assertEquals(['status' => 'accepted', 'customer_id' => 1387], $ack);
    }

    public function testSyncWithoutCustomerScopesToEveryone(): void
    {
        $transport = $this->fakeTransport(
            json_encode(['data' => ['status' => 'accepted', 'customer_id' => null]]),
            202
        );
        $client = $this->clientWith($transport);

        $client->calleridReputation->sync();

        $this->assertEquals('{"customer_id":null}', $transport->last_body);
    }

    public function testSyncRaisesWhenDisabled(): void
    {
        // el backend responde 400 si el sync esta deshabilitado en los settings
        $this->expectException(\Cloudpbx\Protocol\Error\RequestError::class);

        $transport = $this->fakeTransport(
            json_encode(['error' => 'callerid reputation sync is disabled']),
            400
        );
        $client = $this->clientWith($transport);

        $client->calleridReputation->sync();
    }

    public function testCredentialsListsMaskedKeys(): void
    {
        $transport = $this->fakeTransport(json_encode(['data' => [
            ['customer_id' => 1387, 'enabled' => true, 'api_key' => 'ab12****yz89'],
            ['customer_id' => 1400, 'enabled' => false, 'api_key' => null]
        ]]));
        $client = $this->clientWith($transport);

        $credentials = $client->calleridReputation->credentials();

        $this->assertEquals(
            'https://api.example.com/api/v1/management/callerid_reputation/credentials',
            $transport->last_url
        );
        $this->assertCount(2, $credentials);
        $this->assertInstanceOf(
            \Cloudpbx\Sdk\Model\CalleridReputationCredential::class,
            $credentials[0]
        );
        $this->assertEquals(1387, $credentials[0]->customer_id);
        $this->assertTrue($credentials[0]->enabled);
        $this->assertEquals('ab12****yz89', $credentials[0]->api_key);
        $this->assertNull($credentials[1]->api_key);
    }

    public function testCredentialFetchesOneCustomer(): void
    {
        $transport = $this->fakeTransport(json_encode(['data' => [
            'customer_id' => 1387, 'enabled' => true, 'api_key' => 'ab12****yz89'
        ]]));
        $client = $this->clientWith($transport);

        $credential = $client->calleridReputation->credential(1387);

        $this->assertEquals(
            'https://api.example.com/api/v1/management/callerid_reputation/credentials/1387',
            $transport->last_url
        );
        $this->assertEquals(1387, $credential->customer_id);
    }

    public function testSaveCredentialUpsertsWithPut(): void
    {
        $transport = $this->fakeTransport(json_encode(['data' => [
            'customer_id' => 1387, 'enabled' => true, 'api_key' => 'ab12****yz89'
        ]]));
        $client = $this->clientWith($transport);

        $client->calleridReputation->saveCredential(1387, ['api_key' => 'secret', 'enabled' => true]);

        $this->assertEquals('PUT', $transport->last_method);
        $this->assertEquals(
            'https://api.example.com/api/v1/management/callerid_reputation/credentials/1387',
            $transport->last_url
        );
        $this->assertEquals('{"api_key":"secret","enabled":true}', $transport->last_body);
    }

    public function testCredentialRejectsNonIntegerCustomerId(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $transport = $this->fakeTransport(json_encode(['data' => []]));
        $client = $this->clientWith($transport);

        /** @phpstan-ignore-next-line intentional wrong type */
        $client->calleridReputation->credential('1387');
    }
}

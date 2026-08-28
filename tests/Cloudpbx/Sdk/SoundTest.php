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

class SoundTest extends TestCase
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

    public function testDownloadReturnsTheBodyVerbatim(): void
    {
        // bytes crudos, incluido un NUL: el binario no debe pasar por json_decode
        $audio = "OggS\x00\x02\x01\x02\x03";
        $transport = $this->fakeTransport($audio);
        $client = $this->clientWith($transport);

        $content = $client->sounds->download(2, 3);

        $this->assertEquals('GET', $transport->last_method);
        $this->assertEquals(
            'https://api.example.com/api/v1/management/customers/2/sounds/3/download',
            $transport->last_url
        );
        $this->assertSame($audio, $content);
    }

    public function testDownloadRaisesNotFoundWhenSoundIsMissing(): void
    {
        $transport = $this->fakeTransport('{"errors":{"detail":"Not Found"}}', 404);
        $client = $this->clientWith($transport);

        $this->expectException(\Cloudpbx\Protocol\Error\NotFoundError::class);

        $client->sounds->download(2, 999999);
    }
}

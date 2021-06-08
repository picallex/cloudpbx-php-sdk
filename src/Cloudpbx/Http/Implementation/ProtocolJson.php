<?php

// Copyright 2021 Picallex Holding Group. All rights reserved.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Http\Implementation;

use Cloudpbx\Sdk\Util;

final class ProtocolJson implements \Cloudpbx\Http\Protocol
{
    /**
     * @var string
     */
    private $api_key;

    /**
     * @param string $api_key
     */
    public function __construct($api_key)
    {
        $this->api_key = $api_key;
    }

    /**
     * @param array<string, mixed> $headers
     *
     * @return array<string, mixed>
     */
    public function setHeaders($headers)
    {
        Util\Argument::isArray($headers);

        return array_merge(
            $headers,
            ['x-api-key' => $this->api_key],
            ['content-type' => 'application/json'],
            ['accept' => 'application/json, plain/text']
        );
    }

    /**
     * @param string $body
     * @param int $http_code
     *
     * @return \Cloudpbx\Http\Response
     */
    public function buildResponse($body, $http_code)
    {
        return new class($body, $http_code) implements \Cloudpbx\Http\Response {
            /**
             * @var string
             */
            private $body;

            /**
             * @var int
             */
            private $http_code;

            /**
             * @param string $body
             * @param int $http_code
             */
            public function __construct($body, $http_code)
            {
                $this->body = json_decode($body, true) ?? $body;
                $this->http_code = $http_code;
            }

            /**
             * @return int
             */
            public function statusCode()
            {
                return $this->http_code;
            }

            /**
             * @return mixed
             */
            public function body()
            {
                return $this->body;
            }
        };
    }
}

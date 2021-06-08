<?php

// Copyright 2021 Picallex Holding Group. All rights reserved.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Http\Implementation;

use Cloudpbx\Sdk\Util;

use Cloudpbx\Http;

class ClientCurl implements Http\Client
{
    /**
     *@var string
     */
    private $base;

    /**
     *@var Http\Protocol
     */
    private $protocol;

    public function __construct(string $base, Http\Protocol $protocol)
    {
        Util\Argument::isString($base);
        Util\Argument::isInstanceOf($protocol, Http\Protocol::class);

        $this->protocol = $protocol;
        $this->base = $base;
    }

    public function sendRequest(Http\Request $request): Http\Response
    {
        $url = $this->base . $request->url();

        $headers = $this->protocol->setHeaders($request->headers());

        [$body, $http_code] = $this->curlGet($url, $headers);

        return $this->protocol->buildResponse($body, $http_code);
    }

    /**
     * @param string $url
     * @param array<string, mixed> $headers
     * @return array{0: string, 1: int}
     */
    private function curlGet(string $url, array $headers)
    {
        return $this->curl($url, function ($ch) use ($headers) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->buildCurlHeaders($headers));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            return curl_exec($ch);
        });
    }

    /**
     * @param array<string, mixed> $headers
     * @return array<string>
     */
    private function buildCurlHeaders(array $headers)
    {
        $out = [];
        foreach ($headers as $k => $v) {
            $out[] = "{$k}: {$v}";
        }
        return $out;
    }

    /**
     * @return array{0: string, 1: int}
     */
    private function curl(string $url, callable $yield)
    {
        $ch = curl_init($url);
        if ($ch === false) {
            throw new \RuntimeException('failed to curl_init');
        }

        $output = null;
        $http_code = 500;
        $curl_error = null;

        curl_setopt($ch, CURLOPT_TIMEOUT, 3);

        try {
            $output = $yield($ch);
        } finally {
            $http_code = intval(curl_getinfo($ch, CURLINFO_HTTP_CODE));
            $curl_error = curl_error($ch);

            curl_close($ch);
        }

        if ($output === false and $http_code === 0) {
            throw new \RuntimeException('curl failed ' . $curl_error);
        }

        return [$output, $http_code];
    }
}

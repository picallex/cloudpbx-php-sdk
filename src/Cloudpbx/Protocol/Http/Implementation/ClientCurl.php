<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Protocol\Http\Implementation;

use Cloudpbx\Sdk\Util;

use Cloudpbx\Protocol\Http;

class ClientCurl implements Http\Client
{
    public function sendRequest(Http\Request $request): Http\Response
    {
        $requester_of = [
            'GET' => 'curlGet',
            'POST' => 'curlPost',
            'PUT' => 'curlPut',
            'DELETE' => 'curlDelete'
        ];

        if (!in_array($request->method(), array_keys($requester_of))) {
            throw new ClientCurlError("not implementation for method {$request->method()}");
        }

        $requester = $requester_of[$request->method()];
        [$body, $http_code] = $this->$requester(
            $request->url(),
            $request->headers(),
            $request->body()
        );

        return $this->buildResponse($body, $http_code);
    }

    /**
     * @param string $url
     * @param array<string, mixed> $headers
     * @param string | null $body
     * @return array{0: string, 1: int}
     */
    private function curlGet(string $url, array $headers, $body = null)
    {
        return $this->curlRequest(
            'GET',
            $url,
            $headers,
            $body
        );
    }

    /**
     * @param string $url
     * @param array<string, mixed> $headers
     * @param string | null $body
     * @return array{0: string, 1: int}
     */
    private function curlPost($url, $headers, $body = null)
    {
        return $this->curlRequest(
            'POST',
            $url,
            $headers,
            $body,
            $this->curlOption(CURLOPT_POST, true)
        );
    }

    /**
     * @param string $url
     * @param array<string, mixed> $headers
     * @param string | null $body
     * @return array{0: string, 1: int}
     */
    private function curlPut($url, $headers, $body = null)
    {
        return $this->curlRequest(
            'PUT',
            $url,
            $headers,
            $body,
            $this->curlOption(CURLOPT_POST, true)
        );
    }

    /**
     * @param string $url
     * @param array<string, mixed> $headers
     * @param string | null $body
     * @return array{0: string, 1: int}
     */
    private function curlDelete(string $url, array $headers, $body = null)
    {
        return $this->curlRequest('DELETE', $url, $headers, $body);
    }

    /**
     * @param string $method
     * @param string $url
     * @param array<string, mixed> $headers
     * @param string | null $body
     * @param callable(mixed $curl_resource): void $options
     * @return array{0: string, 1: int}
     */
    private function curlRequest($method, $url, $headers, $body = null, ...$options)
    {
        return $this->curl($url, function ($ch) use ($method, $headers, $body, $options) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->buildCurlHeaders($headers));
            if (!is_null($body)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
            }
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            foreach ($options as $option) {
                $option($ch);
            }
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
            throw new ClientCurlError('failed to curl_init');
        }

        $output = null;
        $http_code = 500;
        $curl_error = null;

        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        try {
            $output = $yield($ch);
        } finally {
            $http_code = intval(curl_getinfo($ch, CURLINFO_HTTP_CODE));
            $curl_error = curl_error($ch);

            curl_close($ch);
        }

        if ($output === false and $http_code < 100) {
            throw new ClientCurlError('curl failed ' . $curl_error);
        }

        return [$output, $http_code];
    }

    /**
     * @param string $body
     * @param int $http_code
     * @return Http\Response
     */
    private function buildResponse($body, $http_code)
    {
        return new ResponseFromArray($body, $http_code);
    }

    /**
     * @param mixed $opts
     * @return callable(mixed $curl_resource): void
     */
    private function curlOption(...$opts)
    {
        return function ($ch) use ($opts) {
            call_user_func_array('curl_setopt', array_merge([$ch], $opts));
        };
    }
}

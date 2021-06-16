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
        switch ($request->method()) {
        case 'GET':
            [$body, $http_code] = $this->curlGet(
                $request->url(),
                $request->headers()
            );
            break;
        case 'POST':
            [$body, $http_code] = $this->curlPost(
                $request->url(),
                $request->headers(),
                $request->body()
            );
            break;
        case 'DELETE':
            [$body, $http_code] = $this->curlDelete(
                $request->url(),
                $request->headers()
            );
            break;
        default:
            throw new ClientCurlError("not implementation for method {$request->method()}");
        }

        return $this->buildResponse($body, $http_code);
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
     * @param string $url
     * @param array<string, mixed> $headers
     * @param string | null $body
     * @return array{0: string, 1: int}
     */
    private function curlPost($url, $headers, $body = null)
    {
        return $this->curl($url, function ($ch) use ($headers, $body) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->buildCurlHeaders($headers));
            if (!is_null($body)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
            }
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            return curl_exec($ch);
        });
    }

    /**
     * @param string $url
     * @param array<string, mixed> $headers
     * @return array{0: string, 1: int}
     */
    private function curlDelete(string $url, array $headers)
    {
        return $this->curl($url, function ($ch) use ($headers) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
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
            throw new ClientCurlError('failed to curl_init');
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
}

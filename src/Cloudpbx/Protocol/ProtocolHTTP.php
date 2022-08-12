<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Protocol;

use Cloudpbx\Util;

final class ProtocolHTTP implements \Cloudpbx\Sdk\Protocol
{
    /**
     * @var Http\Client
     */
    private $transport;

    /**
     * @var string
     */
    private $api_base;

    /**
     * @var string
     */
    private $api_key;

    /**
     * @param string $api_base
     * @param string $api_key
     * @param Http\Client $transport
     */
    public function __construct($api_base, $api_key, $transport)
    {
        $this->api_base = $api_base;
        $this->api_key = $api_key;
        $this->transport = $transport;
    }

    /**
     * Crear protocol con cliente curl.
     *
     * @param string $api_base
     * @param string $api_key
     * @return self
     */
    public static function createWithDefaultClient($api_base, $api_key)
    {
        $transport = new Http\Implementation\ClientCurl();
        return new self($api_base, $api_key, $transport);
    }

    public function prepareQuery($url, $params = [])
    {
        return \strtr($url, $params);
    }


    public function list($query)
    {
        $request = Http\Implementation\RequestFromArray::build('GET', [
            'body' => null,
            'headers' => $this->setHeaders([]),
            'url' => $this->api_base . $query
        ]);

        $response = $this->transport->sendRequest($request);

        $this->checkResponse($response);

        $data = json_decode($response->body(), true)["data"] ?? [];
        return $data;
    }

    public function one($query)
    {
        $request = Http\Implementation\RequestFromArray::build('GET', [
            'body' => null,
            'headers' => $this->setHeaders([]),
            'url' => $this->api_base . $query
        ]);

        $response = $this->transport->sendRequest($request);

        $this->checkResponse($response);

        $data = json_decode($response->body(), true)["data"] ?? [];
        return $data;
    }

    public function create($query, $params = null)
    {
        return $this->doRequest('POST', $query, $params);
    }

    public function createWithRaw($query, $content, $headers = [])
    {
        return $this->doRequest('POST', $query, $content, true, false, $headers);
    }

    /**
     * @param string $url
     * @param array<string,mixed> $params
     * @return array<string, mixed>|null
     */
    public function update($query, $params = null)
    {
        return $this->doRequest('PUT', $query, $params);
    }

    public function delete($query, $params = null)
    {
        $this->doRequest('DELETE', $query, $params, false);
    }

    /**
     * @param string $method
     * @param string $url
     * @param array<string,mixed>|null $params
     * @param array<string,mixed>
     * @param boolean $must_process_data
     * @return array<string, mixed>|null
     */
    private function doRequest($method, $url, $params = null, $must_process_data = true, $encode_body = true, $headers = [])
    {
        $body = null;
        if ($encode_body) {
            $body = !is_null($params) ? json_encode($params) : null;
        } else {
            $body = $params;
        }

        $request = Http\Implementation\RequestFromArray::build($method, [
            'body' => $body,
            'headers' => $this->setHeaders($headers),
            'url' => $this->api_base . $url
        ]);

        $response = $this->transport->sendRequest($request);

        $this->checkResponse($response);

        if ($must_process_data) {
            $data = json_decode($response->body(), true)["data"] ?? [];
            return $data;
        } else {
            return null;
        }
    }

    /**
     * @param array<string, mixed> $headers
     *
     * @return array<string, mixed>
     */
    private function setHeaders($headers)
    {
        Util\Argument::isArray($headers);

        return array_merge(
            ['x-api-key' => $this->api_key,
             'content-type' => 'application/json',
             'accept' => 'application/json, plain/text'],
            $headers,
        );
    }

    /**
     * @param \Cloudpbx\Protocol\Http\Response $response
     * @return void
     */
    private function checkResponse($response)
    {
        $status_code = $response->statusCode();

        if ($status_code >= 500) {
            throw new Error\ServerError($status_code, $response->body());
        }

        if ($status_code == 404) {
            throw new Error\NotFoundError('unknown status code');
        }

        if ($status_code >= 400) {
            throw new Error\RequestError($status_code, $response->body());
        }

        if ($status_code >= 300) {
            throw new \RuntimeException("not know how to handle status code {$response->statusCode()}");
        }
    }
}

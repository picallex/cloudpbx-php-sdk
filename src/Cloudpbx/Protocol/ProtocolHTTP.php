<?php

// Copyright 2021 Picallex Holding Group. All rights reserved.
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

    public function list($url)
    {
        $request = Http\Implementation\RequestFromArray::build('GET', [
            'body' => null,
            'headers' => $this->setHeaders([]),
            'url' => $this->api_base . $url
        ]);

        $response = $this->transport->sendRequest($request);

        $this->checkResponse($response);

        $data = json_decode($response->body(), true)["data"] ?? [];
        return $data;
    }

    public function one($url)
    {
        $request = Http\Implementation\RequestFromArray::build('GET', [
            'body' => null,
            'headers' => $this->setHeaders([]),
            'url' => $this->api_base . $url
        ]);

        $response = $this->transport->sendRequest($request);

        $this->checkResponse($response);

        $data = json_decode($response->body(), true)["data"] ?? [];
        return $data;
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
            $headers,
            ['x-api-key' => $this->api_key],
            ['content-type' => 'application/json'],
            ['accept' => 'application/json, plain/text']
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
            throw new Error\ServerError($status_code);
        }

        if ($status_code == 404) {
            throw new Error\NotFoundError('unknown status code');
        }

        if ($status_code >= 400) {
            throw new Error\RequestError($status_code);
        }

        if ($status_code >= 300) {
            throw new \RuntimeException("not know how to handle status code {$response->statusCode()}");
        }
    }
}

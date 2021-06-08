<?php

// Copyright 2021 Picallex Holding Group. All rights reserved.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk;

final class Customer
{
    /**
     * @var \Cloudpbx\Http\Client
     */
    private $transport;

    /**
     * @param \Cloudpbx\Http\Client $transport
     *
     * @return self
     */
    public static function fromTransport($transport)
    {
        Util\Argument::isInstanceOf($transport, \Cloudpbx\Http\Client::class);

        $obj = new self();
        $obj->transport = $transport;
        return $obj;
    }

    /**
     * @return array<\Cloudpbx\Sdk\Model\Customer>
     */
    public function all()
    {
        $request = \Cloudpbx\Http\Implementation\RequestFromArray::build('GET', [
            'body' => null,
            'headers' => [],
            'url' => '/api/v1/management/customers'
        ]);

        $response = $this->transport->sendRequest($request);

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

        $records = $response->body()['data'];
        return array_map([\Cloudpbx\Sdk\Model\Customer::class, 'fromArray'], $records);
    }
}

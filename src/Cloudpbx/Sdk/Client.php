<?php

// Copyright 2021 Picallex Holding Group. All rights reserved.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk;

/**
 * @property Customer $customers
 */
final class Client
{
    /**
     * @var \Cloudpbx\Http\Client
     */
    private $transport;

    /**
     * @param \Cloudpbx\Http\Client $transport
     */
    public function __construct($transport)
    {
        $this->transport = $transport;
    }

    /**
     * @return Customer
     */
    public function getCustomers()
    {
        return Customer::fromTransport($this->transport);
    }

    public function __get(string $name): object
    {
        $method = 'get'.ucfirst($name);

        if (method_exists($this, $method)) {
            return $this->$method();
        }

        throw new \RuntimeException('not found API ' . $name);
    }
}

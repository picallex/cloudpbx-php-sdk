<?php

// Copyright 2021 Picallex Holding Group. All rights reserved.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk;

use Cloudpbx\Util;

final class Customer
{
    /**
     * @var \Cloudpbx\Sdk\Protocol
     */
    private $protocol;

    /**
     * @param \Cloudpbx\Sdk\Protocol $protocol
     *
     * @return self
     */
    public static function fromTransport($protocol)
    {
        Util\Argument::isInstanceOf($protocol, \Cloudpbx\Sdk\Protocol::class);

        $obj = new self();
        $obj->protocol = $protocol;
        return $obj;
    }

    /**
     * @return array<\Cloudpbx\Sdk\Model\Customer>
     */
    public function all()
    {
        $query = $this->protocol->prepareQuery('/api/v1/management/customers');

        $records = $this->protocol->list(
            $query
        );

        return array_map([\Cloudpbx\Sdk\Model\Customer::class, 'fromArray'], $records);
    }
}

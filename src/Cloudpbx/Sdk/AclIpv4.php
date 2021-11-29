<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk;

interface AclIpv4
{
    /**
     * @param int $customer_id
     *
     * @return array<\Cloudpbx\Sdk\Model\AclIpv4>
     */
    public function all($customer_id);

    /**
     * @param int $customer_id
     * @param string $cidr
     *
     * @return \Cloudpbx\Sdk\Model\AclIpv4
     */
    public function create($customer_id, $cidr);

    /**
     * @param int $customer_id
     * @param string $cidr
     *
     * @return void
     */
    public function delete($customer_id, $cidr);
}

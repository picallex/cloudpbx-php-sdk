<?php

// Copyright 2021 Picallex Holding Group. All rights reserved.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk;

interface Customer
{
    /**
     * @return array<\Cloudpbx\Sdk\Model\Customer>
     */
    public function all();

    /**
     * @param int $id
     * @return \Cloudpbx\Sdk\Model\Customer
     */
    public function show($id);

    /**
     * See **ClientCurlTest** for details.
     *
     * @param array<string,mixed> $params
     * @return \Cloudpbx\Sdk\Model\Customer
     */
    public function create($params);

    /**
     * See **ClientCurlTest** for details.
     *
     * @param int $id
     * @param array<string,mixed> $params
     * @return \Cloudpbx\Sdk\Model\Customer
     */
    public function update($id, $params);

    /**
     * @param integer $customer_id
     * @return array<\Cloudpbx\Sdk\Model\Customer\Capability>
     */
    public function capabilities($customer_id);

    /**
     * @param integer $customer_id
     * @param string $capability
     * @return array<\Cloudpbx\Sdk\Model\Customer\Capability>
     */
    public function enable_capability($customer_id, $capability);

    /**
     * @param integer $customer_id
     * @param string $capability
     * @return array<\Cloudpbx\Sdk\Model\Customer\Capability>
     */
    public function disable_capability($customer_id, $capability);
}

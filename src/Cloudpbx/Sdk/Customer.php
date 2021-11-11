<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk;

use Cloudpbx\Util\Argument;

final class Customer extends Api
{
    /**
     * @return array<\Cloudpbx\Sdk\Model\Customer>
     */
    public function all()
    {
        $query = $this->protocol->prepareQuery('/api/v1/management/customers');

        $records = $this->protocol->list(
            $query
        );

        return $this->recordsToModel($records, \Cloudpbx\Sdk\Model\Customer::class);
    }

    /**
     * @param int $id
     * @return \Cloudpbx\Sdk\Model\Customer
     */
    public function show($id)
    {
        $query = $this->protocol->prepareQuery('/api/v1/management/customers/{customer_id}', ['{customer_id}' => $id]);
        $record = $this->protocol->one($query);

        return $this->recordToModel($record, \Cloudpbx\Sdk\Model\Customer::class);
    }

    /**
     * See **ClientCurlTest** for details.
     *
     * @param array<string,mixed> $params
     * @return \Cloudpbx\Sdk\Model\Customer
     */
    public function create($params)
    {
        Argument::isParams($params);

        $query = $this->protocol->prepareQuery('/api/v1/management/customers');
        $record = $this->protocol->create($query, ['customer' => $params]);

        return $this->recordToModel($record, \Cloudpbx\Sdk\Model\Customer::class);
    }

    /**
     * See **ClientCurlTest** for details.
     *
     * @param int $id
     * @param array<string,mixed> $params
     * @return \Cloudpbx\Sdk\Model\Customer
     */
    public function update($id, $params)
    {
        Argument::isInteger($id);
        Argument::isParams($params);

        $query = $this->protocol->prepareQuery('/api/v1/management/customers/{customer_id}', ['{customer_id}' => $id]);
        $record = $this->protocol->update($query, ['customer' => $params]);

        return $this->recordToModel($record, \Cloudpbx\Sdk\Model\Customer::class);
    }


    /**
     * @param integer $customer_id
     * @return array<Model\Customer\Capability>
     */
    public function capabilities($customer_id)
    {
        Argument::isInteger($customer_id);

        $query = $this->protocol->prepareQuery('/api/v1/management/customers/{customer_id}/capabilities', ['{customer_id}' => $customer_id]);

        $records = $this->protocol->list($query);

        return $this->recordsToModel($records, Model\Customer\Capability::class);
    }

    /**
     * @param integer $customer_id
     * @param string $capability
     * @return array<Model\Customer\Capability>
     */
    public function enable_capability($customer_id, $capability)
    {
        Argument::isInteger($customer_id);
        Argument::isString($capability);

        $query = $this->protocol->prepareQuery('/api/v1/management/customers/{customer_id}/capabilities/enable', ['{customer_id}' => $customer_id]);
        $record = $this->protocol->update($query, ['capability' => $capability]);

        return $this->recordToModel($record, Model\Customer\Capability::class);
    }

    /**
     * @param integer $customer_id
     * @param string $capability
     * @return array<Model\Customer\Capability>
     */
    public function disable_capability($customer_id, $capability)
    {
        Argument::isInteger($customer_id);
        Argument::isString($capability);

        $query = $this->protocol->prepareQuery('/api/v1/management/customers/{customer_id}/capabilities/disable', ['{customer_id}' => $customer_id]);
        $record = $this->protocol->update($query, ['capability' => $capability]);

        return $this->recordToModel($record, Model\Customer\Capability::class);
    }
}

<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk\Implementation;

use Cloudpbx\Util\Argument;

final class Customer extends \Cloudpbx\Sdk\Api implements \Cloudpbx\Sdk\Customer
{
    public function all()
    {
        $query = $this->protocol->prepareQuery('/api/v1/management/customers');

        $records = $this->protocol->list(
            $query
        );

        return $this->recordsToModel($records, \Cloudpbx\Sdk\Model\Customer::class);
    }

    public function show($id)
    {
        $query = $this->protocol->prepareQuery('/api/v1/management/customers/{customer_id}', ['{customer_id}' => $id]);
        $record = $this->protocol->one($query);

        return $this->recordToModel($record, \Cloudpbx\Sdk\Model\Customer::class);
    }

    public function create($params)
    {
        Argument::isParams($params);

        $query = $this->protocol->prepareQuery('/api/v1/management/customers');
        $record = $this->protocol->create($query, ['customer' => $params]);

        return $this->recordToModel($record, \Cloudpbx\Sdk\Model\Customer::class);
    }

    public function update($id, $params)
    {
        Argument::isInteger($id);
        Argument::isParams($params);

        $query = $this->protocol->prepareQuery('/api/v1/management/customers/{customer_id}', ['{customer_id}' => $id]);
        $record = $this->protocol->update($query, ['customer' => $params]);

        return $this->recordToModel($record, \Cloudpbx\Sdk\Model\Customer::class);
    }


    public function capabilities($customer_id)
    {
        Argument::isInteger($customer_id);

        $query = $this->protocol->prepareQuery('/api/v1/management/customers/{customer_id}/capabilities', ['{customer_id}' => $customer_id]);

        $records = $this->protocol->list($query);

        return $this->recordsToModel($records, \Cloudpbx\Sdk\Model\Customer\Capability::class);
    }

    public function enable_capability($customer_id, $capability)
    {
        Argument::isInteger($customer_id);
        Argument::isString($capability);

        $query = $this->protocol->prepareQuery('/api/v1/management/customers/{customer_id}/capabilities/enable', ['{customer_id}' => $customer_id]);
        $record = $this->protocol->update($query, ['capability' => $capability]);

        return $this->recordToModel($record, \Cloudpbx\Sdk\Model\Customer\Capability::class);
    }

    public function disable_capability($customer_id, $capability)
    {
        Argument::isInteger($customer_id);
        Argument::isString($capability);

        $query = $this->protocol->prepareQuery('/api/v1/management/customers/{customer_id}/capabilities/disable', ['{customer_id}' => $customer_id]);
        $record = $this->protocol->update($query, ['capability' => $capability]);

        return $this->recordToModel($record, \Cloudpbx\Sdk\Model\Customer\Capability::class);
    }
}

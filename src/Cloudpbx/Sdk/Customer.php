<?php

// Copyright 2021 Picallex Holding Group. All rights reserved.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk;

use Cloudpbx\Util;

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
}

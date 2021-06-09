<?php

// Copyright 2021 Picallex Holding Group. All rights reserved.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk;

use Cloudpbx\Util\Argument;

final class User extends Api
{
    /**
     * @param int $customer_id
     * @return array<\Cloudpbx\Sdk\Model\User>
     */
    public function all($customer_id)
    {
        Argument::isInteger($customer_id);

        $query = $this->protocol->prepareQuery('/api/v1/management/customers/{customer_id}/users', ['{customer_id}' => $customer_id]);

        $records = $this->protocol->list(
            $query
        );

        return $this->recordsToModel($records, Model\User::class);
    }

    /**
     * @param int $customer_id
     * @param int $id
     *
     * @return \Cloudpbx\Sdk\Model\User
     */
    public function show($customer_id, $id)
    {
        Argument::isInteger($customer_id);
        Argument::isInteger($id);

        $query = $this->protocol->prepareQuery('/api/v1/management/customers/{customer_id}/users/{user_id}', [
            '{customer_id}' => $customer_id,
            '{user_id}' => $id
        ]);

        $record = $this->protocol->list(
            $query
        );

        return $this->recordToModel($record, Model\User::class);
    }
}

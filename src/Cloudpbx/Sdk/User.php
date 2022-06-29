<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk;

use Cloudpbx\Util\Argument;

class User extends Api
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


    /**
     * See **ClientCurlTest** for details.
     *
     * @param int $customer_id
     * @param array<string,mixed> $params
     *
     * @return \Cloudpbx\Sdk\Model\User
     */
    public function create($customer_id, $params)
    {
        Argument::isInteger($customer_id);
        Argument::isParams($params);

        $query = $this->protocol->prepareQuery('/api/v1/management/customers/{customer_id}/users', [
            '{customer_id}' => $customer_id
        ]);

        $record = $this->protocol->create($query, ['user' => $params]);

        return $this->recordToModel($record, Model\User::class);
    }

    /**
     * See **ClientCurlTest** for details.
     *
     * @param int $customer_id
     * @param int $id
     * @param array<string,mixed> $params
     *
     * @return \Cloudpbx\Sdk\Model\User
     */
    public function update($customer_id, $id, $params)
    {
        Argument::isInteger($customer_id);
        Argument::isInteger($id);
        Argument::isParams($params);

        $query = $this->protocol->prepareQuery('/api/v1/management/customers/{customer_id}/users/{user_id}', [
            '{customer_id}' => $customer_id,
            '{user_id}' => $id
        ]);

        $record = $this->protocol->update($query, ['user' => $params]);

        return $this->recordToModel($record, Model\User::class);
    }

    /**
     * See **ClientCurlTest** for details.
     *
     * @param int $customer_id
     * @param int $id
     *
     * @return void
     */
    public function delete($customer_id, $id)
    {
        Argument::isInteger($customer_id);
        Argument::isInteger($id);

        $query = $this->protocol->prepareQuery('/api/v1/management/customers/{customer_id}/users/{user_id}', [
            '{customer_id}' => $customer_id,
            '{user_id}' => $id
        ]);

        $this->protocol->delete($query);

        return;
    }
}

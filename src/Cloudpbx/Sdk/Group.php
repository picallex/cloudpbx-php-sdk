<?php

// Copyright 2021 Picallex Holding Group. All rights reserved.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk;

use Cloudpbx\Util\Argument;

final class Group extends Api
{
    /**
     * @param int $customer_id
     * @return array<\Cloudpbx\Sdk\Model\Group>
     */
    public function all($customer_id)
    {
        Argument::isInteger($customer_id);

        $query = $this->protocol->prepareQuery('/api/v1/management/customers/{customer_id}/groups', ['{customer_id}' => $customer_id]);

        $records = $this->protocol->list(
            $query
        );

        return $this->recordsToModel($records, Model\Group::class);
    }

    /**
     * @param int $customer_id
     * @param int $id
     *
     * @return \Cloudpbx\Sdk\Model\Group
     */
    public function show($customer_id, $id)
    {
        Argument::isInteger($customer_id);
        Argument::isInteger($id);

        $query = $this->protocol->prepareQuery('/api/v1/management/customers/{customer_id}/groups/{group_id}', [
            '{customer_id}' => $customer_id,
            '{group_id}' => $id
        ]);

        $record = $this->protocol->list(
            $query
        );

        return $this->recordToModel($record, Model\Group::class);
    }


    /**
     * See **ClientCurlTest** for details.
     *
     * @param int $customer_id
     * @param array<string,mixed> $params
     *
     * @return \Cloudpbx\Sdk\Model\Group
     */
    public function create($customer_id, $params)
    {
        Argument::isInteger($customer_id);
        Argument::isParams($params);

        $query = $this->protocol->prepareQuery('/api/v1/management/customers/{customer_id}/groups', [
            '{customer_id}' => $customer_id
        ]);

        $record = $this->protocol->create($query, ['group' => $params]);

        return $this->recordToModel($record, Model\Group::class);
    }

    /**
     * See **ClientCurlTest** for details.
     *
     * @param int $customer_id
     * @param int $id
     * @param array<string,mixed> $params
     *
     * @return \Cloudpbx\Sdk\Model\Group
     */
    public function update($customer_id, $id, $params)
    {
        Argument::isInteger($customer_id);
        Argument::isInteger($id);
        Argument::isParams($params);

        $query = $this->protocol->prepareQuery('/api/v1/management/customers/{customer_id}/groups/{group_id}', [
            '{customer_id}' => $customer_id,
            '{group_id}' => $id
        ]);

        $record = $this->protocol->update($query, ['group' => $params]);

        return $this->recordToModel($record, Model\Group::class);
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

        $query = $this->protocol->prepareQuery('/api/v1/management/customers/{customer_id}/groups/{group_id}', [
            '{customer_id}' => $customer_id,
            '{group_id}' => $id
        ]);

        $this->protocol->delete($query);

        return;
    }

    /**
     * See **ClientCurlTest** for details.
     *
     * @param int $customer_id
     * @param int $group_id
     * @param int $user_id
     *
     * @return void
     */
    public function attach_user($customer_id, $group_id, $user_id)
    {
        Argument::isInteger($customer_id);
        Argument::isInteger($group_id);
        Argument::isInteger($user_id);

        $query = $this->protocol->prepareQuery('/api/v1/management/customers/{customer_id}/groups/{group_id}/attach/user/{user_id}', [
            '{customer_id}' => $customer_id,
            '{group_id}' => $group_id,
            '{user_id}' => $user_id
        ]);

        $this->protocol->create($query, []);

        return;
    }

    /**
     * See **ClientCurlTest** for details.
     *
     * @param int $customer_id
     * @param int $group_id
     * @param int $user_id
     *
     * @return void
     */
    public function detach_user($customer_id, $group_id, $user_id)
    {
        Argument::isInteger($customer_id);
        Argument::isInteger($group_id);
        Argument::isInteger($user_id);

        $query = $this->protocol->prepareQuery('/api/v1/management/customers/{customer_id}/groups/{group_id}/detach/user/{user_id}', [
            '{customer_id}' => $customer_id,
            '{group_id}' => $group_id,
            '{user_id}' => $user_id
        ]);

        $this->protocol->delete($query);

        return;
    }
}

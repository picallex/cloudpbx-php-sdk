<?php

// Copyright 2021 Picallex Holding Group. All rights reserved.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk;

use Cloudpbx\Util\Argument;

final class CalleridGroup extends Api
{
    /**
     * See **ClientCurlTest** for details.
     *
     * @param int $customer_id
     *
     * @return array<Model\CalleridGroup>
     */
    public function all($customer_id)
    {
        $query = $this->protocol->prepareQuery('/api/v1/management/customers/{customer_id}/callerid_groups', [
            '{customer_id}' => $customer_id
        ]);

        $records = $this->protocol->list($query);

        return $this->recordsToModel($records, Model\CalleridGroup::class);
    }


    /**
     * See **ClientCurlTest** for details.
     *
     * @param int $customer_id
     * @param int $id
     *
     * @return Model\CalleridGroup
     */
    public function show($customer_id, $id)
    {
        $query = $this->protocol->prepareQuery(
            '/api/v1/management/customers/{customer_id}/callerid_groups/{id}',
            [
                                                   '{customer_id}' => $customer_id,
                                                   '{id}' => $id
                                               ]
        );
        $record = $this->protocol->one($query);

        return $this->recordToModel($record, Model\CalleridGroup::class);
    }

    /**
     * See **ClientCurlTest** for details.
     *
     * @param int $customer_id
     * @param array<string,mixed> $params
     *
     * @return \Cloudpbx\Sdk\Model\Customer
     */
    public function create($customer_id, $params)
    {
        Argument::isInteger($customer_id);
        Argument::isParams($params);

        $query = $this->protocol->prepareQuery('/api/v1/management/customers/{customer_id}/callerid_groups', ['{customer_id}' => $customer_id]);
        $record = $this->protocol->create($query, ['callerid_group' => $params]);

        return $this->recordToModel($record, Model\CalleridGroup::class);
    }

    /**
     * See **ClientCurlTest** for details.
     *
     * @param int $customer_id
     * @param int $id
     * @param array<string,mixed> $params
     *
     * @return \Cloudpbx\Sdk\Model\Customer
     */
    public function update($customer_id, $id, $params)
    {
        Argument::isInteger($customer_id);
        Argument::isParams($params);

        $query = $this->protocol->prepareQuery('/api/v1/management/customers/{customer_id}/callerid_groups/{id}', ['{customer_id}' => $customer_id, '{id}' => $id]);
        $record = $this->protocol->update($query, ['callerid_group' => $params]);

        return $this->recordToModel($record, Model\CalleridGroup::class);
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

        $query = $this->protocol->prepareQuery('/api/v1/management/customers/{customer_id}/callerid_groups/{id}', [
            '{customer_id}' => $customer_id,
            '{id}' => $id
        ]);

        $this->protocol->delete($query);

        return;
    }
}

<?php

// Copyright 2021 Picallex Holding Group. All rights reserved.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk;

use Cloudpbx\Util\Argument;

class Callerid extends Api
{
    /**
     * See **ClientCurlTest** for details.
     *
     * @param int $customer_id
     * @param int $callerid_group_id
     *
     * @return array<Model\Callerid>
     */
    public function all($customer_id, $callerid_group_id)
    {
        $query = $this->protocol->prepareQuery('/api/v1/management/customers/{customer_id}/callerid_groups/{callerid_group_id}/callerids', [
            '{customer_id}' => $customer_id,
            '{callerid_group_id}' => $callerid_group_id
        ]);

        $records = $this->protocol->list($query);

        return $this->recordsToModel($records, Model\Callerid::class);
    }


    /**
     * See **ClientCurlTest** for details.
     *
     * @param int $customer_id
     * @param int $callerid_group_id
     * @param int $id
     *
     * @return Model\Callerid
     */
    public function show($customer_id, $callerid_group_id, $id)
    {
        Argument::isInteger($customer_id);
        Argument::isInteger($callerid_group_id);

        $query = $this->protocol->prepareQuery(
            '/api/v1/management/customers/{customer_id}/callerid_groups/{callerid_group_id}/callerids/{id}',
            [
                                                   '{customer_id}' => $customer_id,
                                                   '{callerid_group_id}' => $callerid_group_id,
                                                   '{id}' => $id
                                               ]
        );
        $record = $this->protocol->one($query);

        return $this->recordToModel($record, Model\Callerid::class);
    }

    /**
     * See **ClientCurlTest** for details.
     *
     * @param int $customer_id
     * @param int $callerid_group_id
     * @param array<string,mixed> $params
     *
     * @return \Cloudpbx\Sdk\Model\Customer
     */
    public function create($customer_id, $callerid_group_id, $params)
    {
        Argument::isInteger($customer_id);
        Argument::isInteger($callerid_group_id);
        Argument::isParams($params);

        $query = $this->protocol->prepareQuery(
            '/api/v1/management/customers/{customer_id}/callerid_groups/{callerid_group_id}/callerids',
            [
                                                   '{customer_id}' => $customer_id,
                                                   '{callerid_group_id}' => $callerid_group_id
                                               ]
        );
        $record = $this->protocol->create($query, ['callerid' => $params]);

        return $this->recordToModel($record, Model\Callerid::class);
    }

    /**
     * See **ClientCurlTest** for details.
     *
     * @param int $customer_id
     * @param int $callerid_group_id
     * @param int $id
     * @param array<string,mixed> $params
     *
     * @return Model\Callerid
     */
    public function update($customer_id, $callerid_group_id, $id, $params)
    {
        Argument::isInteger($customer_id);
        Argument::isInteger($callerid_group_id);
        Argument::isInteger($id);
        Argument::isParams($params);

        $query = $this->protocol->prepareQuery(
            '/api/v1/management/customers/{customer_id}/callerid_groups/{callerid_group_id}/callerids/{id}',
            [
                                                   '{customer_id}' => $customer_id,
                                                   '{callerid_group_id}' => $callerid_group_id,
                                                   '{id}' => $id]
        );
        $record = $this->protocol->update($query, ['callerid' => $params]);

        return $this->recordToModel($record, Model\Callerid::class);
    }


    /**
     * See **ClientCurlTest** for details.
     *
     * @param int $customer_id
     * @param int $callerid_group_id
     * @param int $id
     *
     * @return void
     */
    public function delete($customer_id, $callerid_group_id, $id)
    {
        Argument::isInteger($customer_id);
        Argument::isInteger($callerid_group_id);
        Argument::isInteger($id);

        $query = $this->protocol->prepareQuery('/api/v1/management/customers/{customer_id}/callerid_groups/{callerid_group_id}/callerids/{id}', [
            '{customer_id}' => $customer_id,
            '{callerid_group_id}' => $callerid_group_id,
            '{id}' => $id
        ]);

        $this->protocol->delete($query);

        return;
    }
}

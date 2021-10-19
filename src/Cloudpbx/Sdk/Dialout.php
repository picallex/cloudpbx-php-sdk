<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk;

use Cloudpbx\Util\Argument;

final class Dialout extends Api
{
    /**
     * @param int $customer_id
     *
     * @return array<\Cloudpbx\Sdk\Model\Dialout>
     */
    public function all($customer_id)
    {
        Argument::isInteger($customer_id);

        $query = $this->protocol->prepareQuery(
            '/api/v1/management/customers/{customer_id}/dialouts',
            ['{customer_id}' => $customer_id]
        );

        $records = $this->protocol->list(
            $query
        );

        return $this->recordsToModel($records, Model\Dialout::class);
    }

    /**
     * @param int $customer_id
     * @param int $id
     *
     * @return Model\Dialout
     */
    public function show($customer_id, $id)
    {
        $query = $this->protocol->prepareQuery(
            '/api/v1/management/customers/{customer_id}/dialouts/{id}',
            [
                '{customer_id}' => $customer_id,
                '{id}' => $id
            ]
        );

        $record = $this->protocol->one($query);

        return $this->recordToModel($record, Model\Dialout::class);
    }

    /**
     * See **ClientCurlTest** for details.
     *
     * @param int $customer_id
     * @param array<string,mixed> $params
     *
     * @return Model\Dialout
     */
    public function create($customer_id, $params)
    {
        Argument::isInteger($customer_id);
        Argument::isParams($params);

        $query = $this->protocol->prepareQuery('/api/v1/management/customers/{customer_id}/dialouts', [
            '{customer_id}' => $customer_id
        ]);

        $record = $this->protocol->create($query, ['dialout' => $params]);

        return $this->recordToModel($record, Model\Dialout::class);
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

        $query = $this->protocol->prepareQuery('/api/v1/management/customers/{customer_id}/dialouts/{id}', [
            '{customer_id}' => $customer_id,
            '{id}' => $id
        ]);

        $this->protocol->delete($query);

        return;
    }

    /**
     * See **ClientCurlTest** for details.
     *
     * @param int $customer_id
     * @param int $group_id
     * @param int $dialout_id
     * @param int $callerid_group_id
     *
     * @return void
     */
    public function attach_callerid_group($customer_id, $group_id, $dialout_id, $callerid_group_id)
    {
        Argument::isInteger($customer_id);
        Argument::isInteger($group_id);
        Argument::isInteger($dialout_id);
        Argument::isInteger($callerid_group_id);

        $query = $this->protocol->prepareQuery('/api/v1/management/customers/{customer_id}/dialout_groups', [
            '{customer_id}' => $customer_id
        ]);

        $record = $this->protocol->create($query, [
            'group_id' => $group_id,
            'dialout_id' => $dialout_id,
            'callerid_group_id' => $callerid_group_id
        ]);

        return;
    }


    /**
     * See **ClientCurlTest** for details.
     *
     * @param int $customer_id
     * @param int $group_id
     * @param int $dialout_id
     * @param int $callerid_group_id
     *
     * @return void
     */
    public function detach_callerid_group($customer_id, $group_id, $dialout_id, $callerid_group_id)
    {
        Argument::isInteger($customer_id);
        Argument::isInteger($group_id);
        Argument::isInteger($dialout_id);
        Argument::isInteger($callerid_group_id);

        $query = $this->protocol->prepareQuery('/api/v1/management/customers/{customer_id}/dialout_groups', [
            '{customer_id}' => $customer_id
        ]);

        $this->protocol->delete($query, [
            'group_id' => $group_id,
            'dialout_id' => $dialout_id,
            'callerid_group_id' => $callerid_group_id
        ]);

        return;
    }
}

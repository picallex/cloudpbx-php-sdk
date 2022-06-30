<?php

/**
 * Copyright 2022 Picallex Holding Group. All rights reserved.
 *
 * @author (2022) Jovany Leandro G.C <jovany@picallex.com>
 */

declare(strict_types=1);

namespace Cloudpbx\Sdk;

use Cloudpbx\Util\Argument;

final class DialoutGroup extends Api
{
    /**
     * See **DialoutGroupTest** for details.
     *
     * @param int $customer_id
     *
     * @return array<\Cloudpbx\Sdk\Model\DialoutGroup>
     */
    public function all($customer_id)
    {
        Argument::isInteger($customer_id);

        $query = $this->protocol->prepareQuery('/api/v1/management/customers/{customer_id}/dialout_groups', [
            '{customer_id}' => $customer_id
        ]);

        $records = $this->protocol->list(
            $query
        );

        return $this->recordsToModel($records, \Cloudpbx\Sdk\Model\DialoutGroup::class);
    }

    /**
     * See **DialoutGroupTest** for details.
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
     * See **DialoutGroupTest** for details.
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

    public function update($customer_id, $group_id, $dialout_id, $callerid_group_id, $params)
    {
        Argument::isInteger($customer_id);
        Argument::isInteger($group_id);
        Argument::isInteger($dialout_id);
        Argument::isInteger($callerid_group_id);
        Argument::isParams($params);

        $query = $this->protocol->prepareQuery('/api/v1/management/customers/{customer_id}/dialout_groups', [
            '{customer_id}' => $customer_id
        ]);

        $params_update = array_merge($params, [
            'group_id' => $group_id,
            'dialout_id' => $dialout_id,
            'callerid_group_id' => $callerid_group_id,
        ]);
        $record = $this->protocol->update($query, $params_update);

        return $this->recordToModel($record, \Cloudpbx\Sdk\Model\DialoutGroup::class);
    }
}

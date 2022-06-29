<?php

// Copyright 2021 Picallex Holding Group. All rights reserved.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk;

use Cloudpbx\Util\Argument;

class CallcenterAgent extends Api
{
    /**
     * @param int $customer_id
     *
     * @return array<\Cloudpbx\Sdk\Model\CallcenterQueue>
     */
    public function all($customer_id)
    {
        Argument::isInteger($customer_id);

        $query = $this->protocol->prepareQuery(
            '/api/v1/management/customers/{customer_id}/callcenter/agents',
            [
                '{customer_id}' => $customer_id
            ]
        );

        $records = $this->protocol->list(
            $query
        );

        return $this->recordsToModel($records, Model\CallcenterAgent::class);
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

        $query = $this->protocol->prepareQuery('/api/v1/management/customers/{customer_id}/callcenter/agents/{id}', [
            '{customer_id}' => $customer_id,
            '{id}' => $id
        ]);

        $this->protocol->delete($query);

        return;
    }
}

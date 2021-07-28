<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk;

use Cloudpbx\Util\Argument;

final class CallcenterQueue extends Api
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
            '/api/v1/management/customers/{customer_id}/service/callcenter/queues',
            ['{customer_id}' => $customer_id]
        );

        $records = $this->protocol->list(
            $query
        );

        return $this->recordsToModel($records, Model\CallcenterQueue::class);
    }

    /**
     * @param int $customer_id
     * @param int|null $callcenter_queue_id if not set query all agents
     *
     * @return array<\Cloudpbx\Sdk\Model\CallcenterAgent>
     */
    public function agents($customer_id, $callcenter_queue_id = null)
    {
        Argument::isInteger($customer_id);
        $has_callcenter_queue = Argument::optional($callcenter_queue_id, 'isInteger');

        if ($has_callcenter_queue) {
            $query = $this->protocol->prepareQuery(
                '/api/v1/management/customers/{customer_id}/service/callcenter/queues/{callcenter_queue_id}/agents',
                [
                    '{customer_id}' => $customer_id,
                    '{callcenter_queue_id}' => $callcenter_queue_id
                ]
            );
        } else {
            $query = $this->protocol->prepareQuery(
                '/api/v1/management/customers/{customer_id}/callcenter/agents',
                [
                    '{customer_id}' => $customer_id
                ]
            );
        }

        $records = $this->protocol->list($query);

        return $this->recordsToModel($records, Model\CallcenterAgent::class);
    }

    /**
     * @param integer $customer_id
     * @param integer $callcenter_queue_id
     *
     * @return Model\CallcenterQueue
     */
    public function show($customer_id, $callcenter_queue_id)
    {
        Argument::isInteger($customer_id);
        Argument::isInteger($callcenter_queue_id);

        $query = $this->protocol->prepareQuery(
            '/api/v1/management/customers/{customer_id}/service/callcenter/queues/{callcenter_queue_id}',
            [
                '{customer_id}' => $customer_id,
                '{callcenter_queue_id}' => $callcenter_queue_id
            ]
        );

        $record = $this->protocol->one($query);

        return $this->recordToModel($record, Model\CallcenterQueue::class);
    }
}

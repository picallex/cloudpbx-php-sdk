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
     * @param int $callcenter_queue_id
     *
     * @return array<\Cloudpbx\Sdk\Model\CallcenterAgent>
     */
    public function agents($customer_id, $callcenter_queue_id)
    {
        Argument::isInteger($customer_id);
        Argument::isInteger($callcenter_queue_id);

        $query = $this->protocol->prepareQuery(
            '/api/v1/management/customers/{customer_id}/service/callcenter/queues/{callcenter_queue_id}/agents',
            [
                '{customer_id}' => $customer_id,
                '{callcenter_queue_id}' => $callcenter_queue_id
            ]
        );

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

    /**
     * @param integer $customer_id
     *
     * @return array<Model\CallcenterTier>
     */
    public function tiers($customer_id)
    {
        Argument::isInteger($customer_id);

        $query = $this->protocol->prepareQuery(
            '/api/v1/management/customers/{customer_id}/callcenter/tiers',
            [
                '{customer_id}' => $customer_id
            ]
        );

        $records = $this->protocol->list($query);

        return $this->recordsToModel($records, Model\CallcenterTier::class);
    }

    /**
     * See **ClientCurlTest** for details.
     *
     * @param int $customer_id
     * @param array<string,mixed> $params
     *
     * @return Model\CallcenterQueue
     */
    public function create($customer_id, $params)
    {
        Argument::isInteger($customer_id);
        Argument::isParams($params);

        $query = $this->protocol->prepareQuery(
            '/api/v1/management/customers/{customer_id}/service/callcenter/queues',
            [
                '{customer_id}' => $customer_id,
            ]
        );

        $record = $this->protocol->create($query, ['callcenter_queue' => $params]);

        return $this->recordToModel($record, Model\CallcenterQueue::class);
    }


    /**
     * See **ClientCurlTest** for details.
     *
     * @param int $customer_id
     * @param int $id
     * @param array{
     *  name: string,
     *  description: string,
     *  strategy: string,
     *  moh_sound_id: int,
     *  max_wait_time: int,
     *  max_wait_time_with_no_agent: int,
     *  max_wait_time_with_no_agent_time_reached: int, //seconds
     *  discard_abandoned_after: int,
     *  skip_agent_with_external_calls: bool,
     * }|[] $params
     *
     * @return Model\CallcenterQueue
     */
    public function update($customer_id, $id, $params)
    {
        Argument::isInteger($customer_id);
        Argument::isInteger($id);
        Argument::isParams($params);

        $query = $this->protocol->prepareQuery(
            '/api/v1/management/customers/{customer_id}/service/callcenter/queues/{callcenter_queue_id}',
            [
                '{customer_id}' => $customer_id,
                '{callcenter_queue_id}' => $id
            ]
        );

        $record = $this->protocol->update($query, ['callcenter_queue' => $params]);

        return $this->recordToModel($record, Model\CallcenterQueue::class);
    }

    /**
     * See **ClientCurlTest** for details.
     *
     * @param int $customer_id
     * @param int $id;
     *
     * @return void
     */
    public function delete($customer_id, $id)
    {
        Argument::isInteger($customer_id);
        Argument::isInteger($id);

        $query = $this->protocol->prepareQuery(
            '/api/v1/management/customers/{customer_id}/service/callcenter/queues/{id}',
            [
                '{customer_id}' => $customer_id,
                '{id}' => $id
            ]
        );

        $this->protocol->delete($query);

        return;
    }


    /**
     * See **ClientCurlTest** for details.
     *
     * @param int $customer_id
     * @param int $callcenter_queue_id
     * @param int $user_id
     * @param array{
     *  ring_timeout: integer,
     *  autologin: bool
     * }|[] $options
     *
     * @return \Cloudpbx\Sdk\Model\CallcenterAgent
     */
    public function callcenter_agent_attach($customer_id, $callcenter_queue_id, $user_id, $options = [])
    {
        Argument::isInteger($customer_id);
        Argument::isInteger($callcenter_queue_id);
        Argument::isInteger($user_id);
        Argument::isParams($options);

        $query = $this->protocol->prepareQuery(
            '/api/v1/management/customers/{customer_id}/service/callcenter/queues/{callcenter_queue_id}/agents/put_user',
            [
                '{customer_id}' => $customer_id,
                '{callcenter_queue_id}' => $callcenter_queue_id
            ]
        );

        $agent_options = empty($options) ? [] : ['agent' => $options];
        $record = $this->protocol->update($query, array_merge(['user' => ['id' => $user_id]], $agent_options));

        return $this->recordToModel($record, Model\CallcenterAgent::class);
    }

    /**
     * See **ClientCurlTest** for details.
     *
     * @param int $customer_id
     * @param int $callcenter_queue_id
     * @param int $user_id
     *
     * @return \Cloudpbx\Sdk\Model\CallcenterAgent
     */
    public function callcenter_agent_detach($customer_id, $callcenter_queue_id, $user_id)
    {
        Argument::isInteger($customer_id);
        Argument::isInteger($callcenter_queue_id);
        Argument::isInteger($user_id);

        $query = $this->protocol->prepareQuery(
            '/api/v1/management/customers/{customer_id}/service/callcenter/queues/{callcenter_queue_id}/agents/pop_user',
            [
                '{customer_id}' => $customer_id,
                '{callcenter_queue_id}' => $callcenter_queue_id
            ]
        );

        $record = $this->protocol->update($query, ['user' => ['id' => $user_id]]);

        return $this->recordToModel($record, Model\CallcenterAgent::class);
    }
}

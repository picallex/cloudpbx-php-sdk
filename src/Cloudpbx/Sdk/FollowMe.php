<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk;

use Cloudpbx\Util\Argument;

class FollowMe extends Api
{
    /**
     * @param int $customer_id
     *
     * @return array<\Cloudpbx\Sdk\Model|FollowMe>
     */
    public function all($customer_id)
    {
        Argument::isInteger($customer_id);

        $query = $this->protocol->prepareQuery('/api/v1/management/customers/{customer_id}/follow_me', [
            '{customer_id}' => $customer_id
        ]);

        $records = $this->protocol->list(
            $query
        );

        return $this->recordsToModel($records, \Cloudpbx\Sdk\Model\FollowMe::class);
    }

    /**
     * @param integer $customer_id
     * @param integer $id
     *
     * @return Model\FollowMe
     */
    public function show($customer_id, $id)
    {
        Argument::isInteger($customer_id);
        Argument::isInteger($id);

        $query = $this->protocol->prepareQuery(
            '/api/v1/management/customers/{customer_id}/follow_me/{id}',
            [
                '{customer_id}' => $customer_id,
                '{id}' => $id
            ]
        );

        $record = $this->protocol->one($query);

        return $this->recordToModel($record, \Cloudpbx\Sdk\Model\FollowMe::class);
    }

    /**
     * See **ClientCurlTest** for details.
     *
     * @param int $customer_id
     * @param array<string,mixed> $params
     *
     * @return Model\FollowMe
     */
    public function create($customer_id, $params)
    {
        Argument::isInteger($customer_id);
        Argument::isParams($params);

        $query = $this->protocol->prepareQuery('/api/v1/management/customers/{customer_id}/follow_me', [
            '{customer_id}' => $customer_id
        ]);

        $record = $this->protocol->create($query, ['follow_me' => $params]);

        return $this->recordToModel($record, Model\FollowMe::class);
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

        $query = $this->protocol->prepareQuery('/api/v1/management/customers/{customer_id}/follow_me/{id}', [
            '{customer_id}' => $customer_id,
            '{id}' => $id
        ]);

        $this->protocol->delete($query);

        return;
    }
}

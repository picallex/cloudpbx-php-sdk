<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk;

use Cloudpbx\Util\Argument;

final class RouterDid extends Api
{
    /**
     * @param int $customer_id
     *
     * @return array<\Cloudpbx\Sdk\Model\RouterDid>
     */
    public function all($customer_id)
    {
        Argument::isInteger($customer_id);

        $query = $this->protocol->prepareQuery(
            '/api/v1/management/customers/{customer_id}/routers/dids',
            ['{customer_id}' => $customer_id]
        );

        $records = $this->protocol->list(
            $query
        );

        return $this->recordsToModel($records, Model\RouterDid::class);
    }

    /**
     * See **ClientCurlTest** for details.
     *
     * @param int $customer_id
     * @param int $user_id
     * @param string $did
     *
     * @return Model\RouterDid
     */
    public function route_to_user($customer_id, $user_id, $did)
    {
        Argument::isInteger($customer_id);
        Argument::isInteger($user_id);
        Argument::isFormat($did, '/\d+/');

        $query = $this->protocol->prepareQuery(
            '/api/v1/management/customers/{customer_id}/routers/dids/{user_id}/user',
            [
                '{customer_id}' => $customer_id,
                '{user_id}' => $user_id
            ]
        );

        $record = $this->protocol->create($query, ['did' => $did]);
        return $this->recordToModel($record, Model\RouterDid::class);
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

        $query = $this->protocol->prepareQuery(
            '/api/v1/management/customers/{customer_id}/routers/dids/{id}',
            [
                '{customer_id}' => $customer_id,
                '{id}' => $id
            ]
        );

        $this->protocol->delete($query);

        return;
    }
}

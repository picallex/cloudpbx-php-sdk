<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk;

use Cloudpbx\Util\Argument;

class RouterDid extends Api
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
     * @param int $follow_me_id
     * @param string $did
     *
     * @return Model\RouterDid
     */
    public function route_to_follow_me($customer_id, $follow_me_id, $did)
    {
        return $this->route_to_resource('/api/v1/management/customers/{customer_id}/routers/dids/{resource_id}/follow_me', $customer_id, $follow_me_id, $did);
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

    /**
     *
     * @param string $query
     * @param int $customer_id
     * @param int $resource_id
     * @param string $did
     *
     * @return Model\RouterDid
     */
    private function route_to_resource($query, $customer_id, $resource_id, $did)
    {
        Argument::isInteger($customer_id);
        Argument::isInteger($resource_id);
        Argument::isFormat($did, '/\d+/');

        $url = $this->protocol->prepareQuery(
            $query,
            [
                '{customer_id}' => $customer_id,
                '{resource_id}' => $resource_id
            ]
        );

        $record = $this->protocol->create($url, ['did' => $did]);
        return $this->recordToModel($record, Model\RouterDid::class);
    }

    /**
     *
     * @param string $query
     * @param array<string,mixed> $query_params
     * @param array<string,mixed> $body
     *
     * @return Model\RouterDid
     */
    private function route_to_resource_with_body($query, $query_params, $body)
    {
        Argument::isString($query);
        Argument::isParams($query_params);
        Argument::isParams($body);

        $url = $this->protocol->prepareQuery($query, $query_params);

        $record = $this->protocol->create($url, $body);
        return $this->recordToModel($record, Model\RouterDid::class);
    }
}

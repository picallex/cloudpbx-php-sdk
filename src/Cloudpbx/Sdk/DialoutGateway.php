<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.
//
// @author (2026) Agustin Serra <agustin@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk;

use Cloudpbx\Util\Argument;

/**
 * Gateways de un dialout (sub-recurso), con su weight. El Switch Manager
 * mueve el gateway primario ajustando estos weights.
 */
final class DialoutGateway extends Api
{
    /**
     * @param int $customer_id
     * @param int $dialout_id
     *
     * @return array<Model\DialoutGateway>
     */
    public function all($customer_id, $dialout_id)
    {
        Argument::isInteger($customer_id);
        Argument::isInteger($dialout_id);

        $records = $this->protocol->list($this->collectionQuery($customer_id, $dialout_id));

        return $this->recordsToModel($records, Model\DialoutGateway::class);
    }

    /**
     * @param int $customer_id
     * @param int $dialout_id
     * @param array<string,mixed> $params
     *
     * @return Model\DialoutGateway
     */
    public function create($customer_id, $dialout_id, $params)
    {
        Argument::isInteger($customer_id);
        Argument::isInteger($dialout_id);
        Argument::isParams($params);

        $record = $this->protocol->create(
            $this->collectionQuery($customer_id, $dialout_id),
            ['dialout_gateway' => $params]
        );

        return $this->recordToModel($record, Model\DialoutGateway::class);
    }

    /**
     * @param int $customer_id
     * @param int $dialout_id
     * @param int $id
     * @param array<string,mixed> $params
     *
     * @return Model\DialoutGateway
     */
    public function update($customer_id, $dialout_id, $id, $params)
    {
        Argument::isInteger($customer_id);
        Argument::isInteger($dialout_id);
        Argument::isInteger($id);
        Argument::isParams($params);

        $record = $this->protocol->update(
            $this->memberQuery($customer_id, $dialout_id, $id),
            ['dialout_gateway' => $params]
        );

        return $this->recordToModel($record, Model\DialoutGateway::class);
    }

    /**
     * @param int $customer_id
     * @param int $dialout_id
     * @param int $id
     *
     * @return void
     */
    public function delete($customer_id, $dialout_id, $id)
    {
        Argument::isInteger($customer_id);
        Argument::isInteger($dialout_id);
        Argument::isInteger($id);

        $this->protocol->delete($this->memberQuery($customer_id, $dialout_id, $id));
    }

    private function collectionQuery(int $customer_id, int $dialout_id): string
    {
        return $this->protocol->prepareQuery(
            '/api/v1/management/customers/{customer_id}/dialouts/{dialout_id}/gateways',
            [
                '{customer_id}' => $customer_id,
                '{dialout_id}' => $dialout_id,
            ]
        );
    }

    private function memberQuery(int $customer_id, int $dialout_id, int $id): string
    {
        return $this->protocol->prepareQuery(
            '/api/v1/management/customers/{customer_id}/dialouts/{dialout_id}/gateways/{id}',
            [
                '{customer_id}' => $customer_id,
                '{dialout_id}' => $dialout_id,
                '{id}' => $id,
            ]
        );
    }
}

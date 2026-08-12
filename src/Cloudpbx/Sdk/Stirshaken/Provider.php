<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.
//
// @author (2026) Agustin Serra <agustin@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk\Stirshaken;

use Cloudpbx\Util\Argument;
use Cloudpbx\Sdk\Model;

/**
 * Proveedores de transito (carriers de salida) del tenant.
 */
final class Provider extends \Cloudpbx\Sdk\Api
{
    /**
     * @return array<Model\Stirshaken\Provider>
     */
    public function all()
    {
        $query = $this->protocol->prepareQuery('/api/v1/providers');

        $records = $this->protocol->listRaw($query);

        return $this->recordsToModel($records, Model\Stirshaken\Provider::class);
    }

    /**
     * @param array<string, mixed> $params
     *
     * @return Model\Stirshaken\Provider
     */
    public function create($params)
    {
        Argument::isArray($params);

        $query = $this->protocol->prepareQuery('/api/v1/providers');

        $record = $this->protocol->createRaw($query, $params);

        return $this->recordToModel($record, Model\Stirshaken\Provider::class);
    }

    /**
     * @param string $id
     * @param array<string, mixed> $params
     *
     * @return Model\Stirshaken\Provider
     */
    public function update($id, $params)
    {
        Argument::isString($id);
        Argument::isArray($params);

        $query = $this->protocol->prepareQuery('/api/v1/providers/{id}', ['{id}' => $id]);

        $record = $this->protocol->updateRaw($query, $params);

        return $this->recordToModel($record, Model\Stirshaken\Provider::class);
    }

    /**
     * @param string $id
     *
     * @return void
     */
    public function delete($id)
    {
        Argument::isString($id);

        $query = $this->protocol->prepareQuery('/api/v1/providers/{id}', ['{id}' => $id]);

        $this->protocol->delete($query);
    }

    /**
     * IPs de destino del proveedor.
     *
     * @param string $provider_id
     *
     * @return array<Model\Stirshaken\ProviderIp>
     */
    public function ips($provider_id)
    {
        Argument::isString($provider_id);

        $query = $this->protocol->prepareQuery('/api/v1/providers/{provider_id}/ips', ['{provider_id}' => $provider_id]);

        $records = $this->protocol->listRaw($query);

        return $this->recordsToModel($records, Model\Stirshaken\ProviderIp::class);
    }

    /**
     * @param string $provider_id
     * @param array<string, mixed> $params
     *
     * @return Model\Stirshaken\ProviderIp
     */
    public function addIp($provider_id, $params)
    {
        Argument::isString($provider_id);
        Argument::isArray($params);

        $query = $this->protocol->prepareQuery('/api/v1/providers/{provider_id}/ips', ['{provider_id}' => $provider_id]);

        $record = $this->protocol->createRaw($query, $params);

        return $this->recordToModel($record, Model\Stirshaken\ProviderIp::class);
    }

    /**
     * @param string $provider_id
     * @param string $ip_id
     *
     * @return void
     */
    public function removeIp($provider_id, $ip_id)
    {
        Argument::isString($provider_id);
        Argument::isString($ip_id);

        $query = $this->protocol->prepareQuery(
            '/api/v1/providers/{provider_id}/ips/{ip_id}',
            ['{provider_id}' => $provider_id, '{ip_id}' => $ip_id]
        );

        $this->protocol->delete($query);
    }
}

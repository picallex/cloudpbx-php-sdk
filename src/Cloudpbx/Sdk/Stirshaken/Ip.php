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
 * IPs autorizadas del tenant en la plataforma stir/shaken.
 *
 * El backend responde con arrays/objetos planos (sin envelope "data"),
 * por eso se usan los metodos *Raw del protocol.
 */
final class Ip extends \Cloudpbx\Sdk\Api
{
    /**
     * @return array<Model\Stirshaken\Ip>
     */
    public function all()
    {
        $query = $this->protocol->prepareQuery('/api/v1/ips');

        $records = $this->protocol->listRaw($query);

        return $this->recordsToModel($records, Model\Stirshaken\Ip::class);
    }

    /**
     * @param array<string, mixed> $params
     *
     * @return Model\Stirshaken\Ip
     */
    public function create($params)
    {
        Argument::isArray($params);

        $query = $this->protocol->prepareQuery('/api/v1/ips');

        $record = $this->protocol->createRaw($query, $params);

        return $this->recordToModel($record, Model\Stirshaken\Ip::class);
    }

    /**
     * @param string $id
     * @param array<string, mixed> $params
     *
     * @return Model\Stirshaken\Ip
     */
    public function update($id, $params)
    {
        Argument::isString($id);
        Argument::isArray($params);

        $query = $this->protocol->prepareQuery('/api/v1/ips/{id}', ['{id}' => $id]);

        $record = $this->protocol->updateRaw($query, $params);

        return $this->recordToModel($record, Model\Stirshaken\Ip::class);
    }

    /**
     * @param string $id
     *
     * @return void
     */
    public function delete($id)
    {
        Argument::isString($id);

        $query = $this->protocol->prepareQuery('/api/v1/ips/{id}', ['{id}' => $id]);

        $this->protocol->delete($query);
    }

    /**
     * Proveedores asignados a una IP.
     *
     * @param string $ip_id
     *
     * @return array<Model\Stirshaken\IpProviderAssignment>
     */
    public function providers($ip_id)
    {
        Argument::isString($ip_id);

        $query = $this->protocol->prepareQuery('/api/v1/ips/{ip_id}/providers', ['{ip_id}' => $ip_id]);

        $records = $this->protocol->listRaw($query);

        return $this->recordsToModel($records, Model\Stirshaken\IpProviderAssignment::class);
    }

    /**
     * @param string $ip_id
     * @param array<string, mixed> $params
     *
     * @return Model\Stirshaken\IpProviderAssignment
     */
    public function assignProvider($ip_id, $params)
    {
        Argument::isString($ip_id);
        Argument::isArray($params);

        $query = $this->protocol->prepareQuery('/api/v1/ips/{ip_id}/providers', ['{ip_id}' => $ip_id]);

        $record = $this->protocol->createRaw($query, $params);

        return $this->recordToModel($record, Model\Stirshaken\IpProviderAssignment::class);
    }

    /**
     * @param string $ip_id
     * @param string $assignment_id
     * @param array<string, mixed> $params
     *
     * @return Model\Stirshaken\IpProviderAssignment
     */
    public function updateProvider($ip_id, $assignment_id, $params)
    {
        Argument::isString($ip_id);
        Argument::isString($assignment_id);
        Argument::isArray($params);

        $query = $this->protocol->prepareQuery(
            '/api/v1/ips/{ip_id}/providers/{assignment_id}',
            ['{ip_id}' => $ip_id, '{assignment_id}' => $assignment_id]
        );

        $record = $this->protocol->patchRaw($query, $params);

        return $this->recordToModel($record, Model\Stirshaken\IpProviderAssignment::class);
    }

    /**
     * @param string $ip_id
     * @param string $assignment_id
     *
     * @return void
     */
    public function unassignProvider($ip_id, $assignment_id)
    {
        Argument::isString($ip_id);
        Argument::isString($assignment_id);

        $query = $this->protocol->prepareQuery(
            '/api/v1/ips/{ip_id}/providers/{assignment_id}',
            ['{ip_id}' => $ip_id, '{assignment_id}' => $assignment_id]
        );

        $this->protocol->delete($query);
    }
}

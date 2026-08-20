<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.

declare(strict_types=1);

namespace Cloudpbx\Sdk;

use Cloudpbx\Util\Argument;

class CalleridReputation extends \Cloudpbx\Sdk\Api
{
    /**
     * Settings efectivos del sync de reputacion (master switch, batch y scheduler).
     *
     * @return Model\CalleridReputationSetting
     */
    public function settings()
    {
        $query = $this->protocol->prepareQuery('/api/v1/management/callerid_reputation/settings');
        $record = $this->protocol->one($query);

        return $this->recordToModel($record, Model\CalleridReputationSetting::class);
    }

    /**
     * Persiste settings (todos los nodos). Claves aceptadas por el backend:
     * enabled, batch_size (1..150), pause_between_batches_ms, scheduler_enabled,
     * run_at_utc_hour (0..23). Devuelve los settings ya efectivos.
     *
     * @param array<string,mixed> $params
     *
     * @return Model\CalleridReputationSetting
     */
    public function updateSettings($params)
    {
        Argument::isParams($params);

        $query = $this->protocol->prepareQuery('/api/v1/management/callerid_reputation/settings');

        // este endpoint recibe los campos en la raiz, sin wrapper por recurso
        $record = $this->protocol->update($query, $params);

        return $this->recordToModel($record, Model\CalleridReputationSetting::class);
    }

    /**
     * Dispara el sync en background (202). Falla con RequestError si el sync
     * esta deshabilitado en los settings.
     *
     * @param int|null $customer_id acota el sync a un customer, null = todos
     *
     * @return array{status: string, customer_id: int|null}
     */
    public function sync($customer_id = null)
    {
        Argument::optional($customer_id, 'isInteger');

        $query = $this->protocol->prepareQuery('/api/v1/management/callerid_reputation/sync');

        return $this->protocol->create($query, ['customer_id' => $customer_id]);
    }

    /**
     * Credenciales de CRM por customer. El api_key viene enmascarado.
     *
     * @return array<Model\CalleridReputationCredential>
     */
    public function credentials()
    {
        $query = $this->protocol->prepareQuery('/api/v1/management/callerid_reputation/credentials');
        $records = $this->protocol->list($query);

        return $this->recordsToModel($records, Model\CalleridReputationCredential::class);
    }

    /**
     * @param int $customer_id
     *
     * @return Model\CalleridReputationCredential
     */
    public function credential($customer_id)
    {
        Argument::isInteger($customer_id);

        $query = $this->protocol->prepareQuery(
            '/api/v1/management/callerid_reputation/credentials/{customer_id}',
            ['{customer_id}' => $customer_id]
        );
        $record = $this->protocol->one($query);

        return $this->recordToModel($record, Model\CalleridReputationCredential::class);
    }

    /**
     * Upsert de la credencial de un customer. Claves: api_key, enabled.
     *
     * ponytail: el backend expone POST /credentials y PUT /credentials/{id} y
     * ambos son upsert, asi que alcanza el PUT.
     *
     * @param int $customer_id
     * @param array<string,mixed> $params
     *
     * @return Model\CalleridReputationCredential
     */
    public function saveCredential($customer_id, $params)
    {
        Argument::isInteger($customer_id);
        Argument::isParams($params);

        $query = $this->protocol->prepareQuery(
            '/api/v1/management/callerid_reputation/credentials/{customer_id}',
            ['{customer_id}' => $customer_id]
        );
        $record = $this->protocol->update($query, $params);

        return $this->recordToModel($record, Model\CalleridReputationCredential::class);
    }
}

<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.
//
// @author (2026) Agustin Serra <agustin@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk\Stirshaken;

use Cloudpbx\Util\Argument;
use Cloudpbx\Sdk\Model;

final class ApiKey extends \Cloudpbx\Sdk\Api
{
    /**
     * @return array<Model\Stirshaken\ApiKey>
     */
    public function all()
    {
        $query = $this->protocol->prepareQuery('/api/v1/api-keys');

        $records = $this->protocol->listRaw($query);

        return $this->recordsToModel($records, Model\Stirshaken\ApiKey::class);
    }

    /**
     * El raw_key solo se devuelve en esta respuesta de creacion, nunca mas.
     *
     * @param string $name
     *
     * @return Model\Stirshaken\ApiKey
     */
    public function create($name)
    {
        Argument::isString($name);

        $query = $this->protocol->prepareQuery('/api/v1/api-keys');

        $record = $this->protocol->createRaw($query, ['name' => $name]);

        return $this->recordToModel($record, Model\Stirshaken\ApiKey::class);
    }

    /**
     * @param string $id
     *
     * @return void
     */
    public function delete($id)
    {
        Argument::isString($id);

        $query = $this->protocol->prepareQuery('/api/v1/api-keys/{id}', ['{id}' => $id]);

        $this->protocol->delete($query);
    }
}

<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.
//
// @author (2026) Agustin Serra <agustin@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk\Stirshaken;

use Cloudpbx\Util\Argument;
use Cloudpbx\Sdk\Model;

final class Certificate extends \Cloudpbx\Sdk\Api
{
    /**
     * @return array<Model\Stirshaken\Certificate>
     */
    public function all()
    {
        $query = $this->protocol->prepareQuery('/api/v1/certificates');

        $records = $this->protocol->listRaw($query);

        return $this->recordsToModel($records, Model\Stirshaken\Certificate::class);
    }

    /**
     * @param array<string, mixed> $params
     *
     * @return Model\Stirshaken\Certificate
     */
    public function create($params)
    {
        Argument::isArray($params);

        $query = $this->protocol->prepareQuery('/api/v1/certificates');

        $record = $this->protocol->createRaw($query, $params);

        return $this->recordToModel($record, Model\Stirshaken\Certificate::class);
    }

    /**
     * @param string $id
     *
     * @return void
     */
    public function delete($id)
    {
        Argument::isString($id);

        $query = $this->protocol->prepareQuery('/api/v1/certificates/{id}', ['{id}' => $id]);

        $this->protocol->delete($query);
    }
}

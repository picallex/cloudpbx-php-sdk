<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk;

use Cloudpbx\Util\Argument;

final class Dialout extends Api
{
    /**
     * @param int $customer_id
     *
     * @return array<\Cloudpbx\Sdk\Model\Dialout>
     */
    public function all($customer_id)
    {
        Argument::isInteger($customer_id);

        $query = $this->protocol->prepareQuery(
            '/api/v1/management/customers/{customer_id}/dialouts',
            ['{customer_id}' => $customer_id]
        );

        $records = $this->protocol->list(
            $query
        );

        return $this->recordsToModel($records, Model\Dialout::class);
    }

    /**
     * @param int $customer_id
     * @param int $id
     *
     * @return Model\Dialout
     */
    public function show($customer_id, $id)
    {
        $query = $this->protocol->prepareQuery(
            '/api/v1/management/customers/{customer_id}/dialouts/{id}',
            [
                '{customer_id}' => $customer_id,
                '{id}' => $id
            ]
        );

        $record = $this->protocol->one($query);

        return $this->recordToModel($record, Model\Dialout::class);
    }
}

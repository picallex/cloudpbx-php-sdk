<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk;

use Cloudpbx\Util\Argument;

class Provisioning extends Api
{
    /**
     * Run provisioning for a customer.
     *
     * Assigns the least-loaded Freeswitch node and creates the DNS record.
     *
     * @param int $customer_id
     *
     * @return Model\Provisioning
     */
    public function run($customer_id)
    {
        Argument::isInteger($customer_id);

        $query = $this->protocol->prepareQuery(
            '/api/v1/management/customers/{customer_id}/provisioning',
            ['{customer_id}' => $customer_id]
        );

        $record = $this->protocol->create($query, null);

        return $this->recordToModel($record, Model\Provisioning::class);
    }
}

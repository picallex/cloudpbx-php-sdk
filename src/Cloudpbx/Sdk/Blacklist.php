<?php

// Copyright 2021 Picallex Holding Group. All rights reserved.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk;

use Cloudpbx\Util\Argument;

class Blacklist extends Api
{
    /**
     * @param int $customer_id
     *
     * @return array<Model\Blacklist>
     */
    public function all($customer_id)
    {
        Argument::isInteger($customer_id);

        $query = $this->protocol->prepareQuery(
            '/api/v1/management/customers/{customer_id}/blacklists',
            ['{customer_id}' => $customer_id]
        );

        $records = $this->protocol->list($query);

        return $this->recordsToModel($records, Model\Blacklist::class);
    }
}

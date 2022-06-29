<?php

/**
 * Copyright 2022 Picallex Holding Group. All rights reserved.
 *
 * @author (2022) Jovany Leandro G.C <jovany@picallex.com>
 */

declare(strict_types=1);

namespace Cloudpbx\Sdk;

use Cloudpbx\Util\Argument;

final class DialoutGroup extends Api
{
    /**
     * See **DialoutGroupTest** for details.
     *
     * @param int $customer_id
     *
     * @return array<\Cloudpbx\Sdk\Model\DialoutGroup>
     */
    public function all($customer_id)
    {
        Argument::isInteger($customer_id);

        $query = $this->protocol->prepareQuery('/api/v1/management/customers/{customer_id}/dialout_groups', [
            '{customer_id}' => $customer_id
        ]);

        $records = $this->protocol->list(
            $query
        );

        return $this->recordsToModel($records, \Cloudpbx\Sdk\Model\DialoutGroup::class);
    }
}

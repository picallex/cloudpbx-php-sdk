<?php

// Copyright 2021 Picallex Holding Group. All rights reserved.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk;

use Cloudpbx\Util\Argument;

final class IvrMenuEntry extends Api
{
    /**
     * @param int $customer_id
     * @param int $ivrmenu_id
     *
     * @return array<\Cloudpbx\Sdk\Model\IvrMenuEntry>
     */
    public function all($customer_id, $ivrmenu_id)
    {
        Argument::isInteger($customer_id);
        Argument::isInteger($ivrmenu_id);

        $query = $this->protocol->prepareQuery('/api/v1/management/customers/{customer_id}/ivr_menus/{ivrmenu_id}/entries', [
            '{customer_id}' => $customer_id,
            '{ivrmenu_id}' => $ivrmenu_id
        ]);

        $records = $this->protocol->list($query);

        return $this->recordsToModel($records, \Cloudpbx\Sdk\Model\IvrMenuEntry::class);
    }
}

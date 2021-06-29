<?php

// Copyright 2021 Picallex Holding Group. All rights reserved.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk;

use Cloudpbx\Util\Argument;

final class IvrMenu extends Api
{
    /**
     * @param int $customer_id
     *
     * @return array<\Cloudpbx\Sdk\Model\IvrMenu>
     */
    public function all($customer_id)
    {
        Argument::isInteger($customer_id);

        $query = $this->protocol->prepareQuery('/api/v1/management/customers/{customer_id}/ivr_menus', [
            '{customer_id}' => $customer_id
        ]);

        $records = $this->protocol->list($query);

        return $this->recordsToModel($records, \Cloudpbx\Sdk\Model\IvrMenu::class);
    }
}

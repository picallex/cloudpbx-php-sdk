<?php

// Copyright 2021 Picallex Holding Group. All rights reserved.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk;

use Cloudpbx\Util\Argument;

class IvrMenuEntry extends Api
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

        return $this->recordsToModel(
            $records,
            \Cloudpbx\Sdk\Model\IvrMenuEntry::class,
            [
                                         'transform' => [
                                             function (&$record, $customer_id) {
                                                 $record['customer_id'] = $customer_id;
                                             },
                                             [$customer_id]
                                         ],
                                     ]
        );
    }

    /**
     * @param integer $customer_id
     * @param integer $ivr_menu_id
     * @param integer $id
     *
     * @return \Cloudpbx\Sdk\Model\IvrMenuEntry
     */
    public function show($customer_id, $ivr_menu_id, $id)
    {
        $query = $this->protocol->prepareQuery('/api/v1/management/customers/{customer_id}/ivr_menus/{ivrmenu_id}/entries/{id}', [
            '{customer_id}' => $customer_id,
            '{ivrmenu_id}' => $ivr_menu_id,
            '{id}' => $id
        ]);

        $record = $this->protocol->one($query);

        return $this->recordToModel($record, \Cloudpbx\Sdk\Model\IvrMenuEntry::class);
    }
}

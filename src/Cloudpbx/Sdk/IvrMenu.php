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

        $query = $this->protocol->prepareQuery(
            '/api/v1/management/customers/{customer_id}/ivr_menus',
            [
            '{customer_id}' => $customer_id
        ]
        );

        $records = $this->protocol->list($query);

        return $this->recordsToModel($records, \Cloudpbx\Sdk\Model\IvrMenu::class);
    }

    /**
     * @param integer $customer_id
     * @param integer $id
     *
     * @return Model\IvrMenu
     */
    public function show($customer_id, $id)
    {
        Argument::isInteger($customer_id);
        Argument::isInteger($id);

        $query = $this->protocol->prepareQuery(
            '/api/v1/management/customers/{customer_id}/ivr_menus/{id}',
            [
                '{customer_id}' => $customer_id,
                '{id}' => $id
            ]
        );

        $record = $this->protocol->one($query);

        return $this->recordToModel($record, Model\IvrMenu::class);
    }

    /**
     * See **ClientCurlTest** for details.
     *
     * @param int $customer_id
     * @param array<string,mixed> $params
     *
     * @return Model\IvrMenu
     */
    public function create($customer_id, $params)
    {
        Argument::isInteger($customer_id);
        Argument::isParams($params);

        $query = $this->protocol->prepareQuery('/api/v1/management/customers/{customer_id}/ivr_menus', [
            '{customer_id}' => $customer_id
        ]);

        $record = $this->protocol->create($query, ['ivr_menu' => $params]);

        return $this->recordToModel($record, Model\IvrMenu::class);
    }

    /**
     * See **ClientCurlTest** for details.
     *
     * @param int $customer_id
     * @param int $id
     *
     * @return void
     */
    public function delete($customer_id, $id)
    {
        Argument::isInteger($customer_id);
        Argument::isInteger($id);

        $query = $this->protocol->prepareQuery('/api/v1/management/customers/{customer_id}/ivr_menus/{id}', [
            '{customer_id}' => $customer_id,
            '{id}' => $id
        ]);

        $this->protocol->delete($query);

        return;
    }
}

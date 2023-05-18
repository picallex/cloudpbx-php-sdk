<?php

// Copyright 2021 Picallex Holding Group. All rights reserved.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk;

use Cloudpbx\Util\Argument;

class Supervisor extends Api
{
    /**
     * @param int $customer_id
     * @return array<Model\Supervisor>
     */
    public function all($customer_id)
    {
        Argument::isInteger($customer_id);

        $query = $this->protocol->prepareQuery(
            '/api/v1/management/customers/{customer_id}/users/supervisors',
            [
                '{customer_id}' => $customer_id
            ]
        );

        $records = $this->protocol->list($query);

        return $this->recordsToModel($records, Model\Supervisor::class);
    }

    /**
     * See **SupervisorTest** for details.
     *
     * @param int $customer_id
     * @param int $user_id
     * @param string $authentication_code only digits 0-9
     *
     * @return void
     */
    public function create($customer_id, $user_id, $authentication_code)
    {
        Argument::isInteger($customer_id);
        Argument::isInteger($user_id);
        Argument::isDigits($authentication_code);

        $query = $this->protocol->prepareQuery('/api/v1/management/customers/{customer_id}/users/{user_id}/supervisors', [
            '{customer_id}' => $customer_id,
            '{user_id}' => $user_id
        ]);

        $record = $this->protocol->create(
            $query,
            [
                                              'supervisor' => [
                                                  'spy_authentication_code' => (string) $authentication_code,
                                                  'spy_allow' => true
                                              ]
                                          ]
        );

        return $this->recordToModel($record, Model\Supervisor::class);
    }

    /**
     * See **SupervisorTest** for details.
     *
     * @param int $customer_id
     * @param int $user_id
     *
     * @return void
     */
    public function delete($customer_id, $user_id)
    {
        Argument::isInteger($customer_id);
        Argument::isInteger($user_id);

        $query = $this->protocol->prepareQuery('/api/v1/management/customers/{customer_id}/users/{user_id}/supervisors', [
            '{customer_id}' => $customer_id,
            '{user_id}' => $user_id
        ]);

        $this->protocol->delete($query);

        return;
    }
}

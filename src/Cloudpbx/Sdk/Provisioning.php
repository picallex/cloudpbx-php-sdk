<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.
//
// @author (2026) Agustin Serra <agustin@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk;

use Cloudpbx\Util\Argument;

final class Provisioning extends Api
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

        $record = $this->protocol->create($query, []);

        return $this->recordToModel($record, Model\Provisioning::class);
    }

    /**
     * List provisioning attempts for a customer (without steps).
     *
     * @param int $customer_id
     *
     * @return array<Model\ProvisioningAttempt>
     */
    public function attempts($customer_id)
    {
        Argument::isInteger($customer_id);

        $query = $this->protocol->prepareQuery(
            '/api/v1/management/customers/{customer_id}/provisioning_attempts',
            ['{customer_id}' => $customer_id]
        );

        $records = $this->protocol->list($query);

        return $this->recordsToModel($records, Model\ProvisioningAttempt::class);
    }

    /**
     * Show a provisioning attempt detail, including its ordered steps.
     *
     * @param int $customer_id
     * @param int $attempt_id
     *
     * @return Model\ProvisioningAttempt
     */
    public function attempt($customer_id, $attempt_id)
    {
        Argument::isInteger($customer_id);
        Argument::isInteger($attempt_id);

        $query = $this->protocol->prepareQuery(
            '/api/v1/management/customers/{customer_id}/provisioning_attempts/{attempt_id}',
            [
                '{customer_id}' => $customer_id,
                '{attempt_id}' => $attempt_id
            ]
        );

        $record = $this->protocol->one($query);

        return $this->recordToModel($record, Model\ProvisioningAttempt::class);
    }
}

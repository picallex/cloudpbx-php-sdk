<?php

// Copyright 2021 Picallex Holding Group. All rights reserved.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk;

use Cloudpbx\Util\Argument;

final class FollowMeEntry extends Api
{
    /**
     * @param int $customer_id
     * @param int $follow_me_id
     *
     * @return array<\Cloudpbx\Sdk\Model\FollowMeEntry>
     */
    public function all($customer_id, $follow_me_id)
    {
        Argument::isInteger($customer_id);
        Argument::isInteger($follow_me_id);

        $query = $this->protocol->prepareQuery('/api/v1/management/customers/{customer_id}/follow_me/{follow_me_id}/entries', [
            '{customer_id}' => $customer_id,
            '{follow_me_id}' => $follow_me_id
        ]);

        $records = $this->protocol->list($query);

        return $this->recordsToModel(
            $records,
            Model\FollowMeEntry::class,
            [
                                         'transform' => [
                                             [$this, 'append_customer_id'],
                                             [$customer_id]
                                         ],
                                     ]
        );
    }

    /**
     * @param integer $customer_id
     * @param integer $follow_me_id
     * @param integer $id
     *
     * @return \Cloudpbx\Sdk\Model\FollowMeEntry
     */
    public function show($customer_id, $follow_me_id, $id)
    {
        $query = $this->protocol->prepareQuery('/api/v1/management/customers/{customer_id}/follow_me/{follow_me_id}/entries/{id}', [
            '{customer_id}' => $customer_id,
            '{follow_me_id}' => $follow_me_id,
            '{id}' => $id
        ]);

        $record = $this->protocol->one($query);

        return $this->recordToModel($record, Model\FollowMeEntry::class);
    }

    /**
     * @param array<string, mixed> $record
     * @param integer $customer_id
     */
    public function append_customer_id(&$record, $customer_id): void
    {
        $record['customer_id'] = $customer_id;
    }
}

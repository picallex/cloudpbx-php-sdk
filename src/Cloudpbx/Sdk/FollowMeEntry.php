<?php

// Copyright 2021 Picallex Holding Group. All rights rseerved.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk;

use Cloudpbx\Util\Argument;

class FollowMeEntry extends Api
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

        return $this->recordsToModel($records, Model\FollowMeEntry::class, $this->default_options($customer_id));
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

        return $this->recordToModel($record, Model\FollowMeEntry::class, $this->default_options($customer_id));
    }

    /**
     * See **ClientCurlTest** for details.
     *
     * @param int $customer_id
     * @param int $follow_me_id
     * @param int $id
     * @param array{
     *  priority: integer,
     *  call_timeout: integer
     * }|[] $options
     *
     * @return Model\FollowMeEntry
     */
    public function update($customer_id, $follow_me_id, $id, $options)
    {
        Argument::isInteger($customer_id);
        Argument::isInteger($follow_me_id);
        Argument::isInteger($id);
        Argument::isParams($options);

        $query = $this->protocol->prepareQuery('/api/v1/management/customers/{customer_id}/follow_me/{follow_me_id}/entries/{id}', [
            '{customer_id}' => $customer_id,
            '{follow_me_id}' => $follow_me_id,
            '{id}' => $id
        ]);

        $record = $this->protocol->update($query, ['follow_me_entry' => $options]);

        return $this->recordToModel($record, Model\FollowMeEntry::class, $this->default_options($customer_id));
    }

    /**
     * @param integer $customer_id
     * @param integer $follow_me_id
     * @param integer $callcenter_queue_id
     * @param array{
     *   priority: integer
     * }|[] $options
     *
     * @return Model\FollowMeEntry
     */
    public function create_callcenter_queue($customer_id, $follow_me_id, $callcenter_queue_id, $options = [])
    {
        Argument::isInteger($customer_id);
        Argument::isInteger($follow_me_id);
        Argument::isInteger($callcenter_queue_id);
        Argument::isParams($options);

        $query = $this->protocol->prepareQuery('/api/v1/management/customers/{customer_id}/follow_me/{follow_me_id}/entries/{callcenter_queue_id}/callcenter_queue', [
            '{customer_id}' => $customer_id,
            '{follow_me_id}' => $follow_me_id,
            '{callcenter_queue_id}' => $callcenter_queue_id
        ]);

        $record = $this->protocol->create($query, ['options' => $options]);

        return $this->recordToModel($record, Model\FollowMeEntry::class, $this->default_options($customer_id));
    }


    /**
     * @param integer $customer_id
     * @param integer $follow_me_id
     * @param integer $dialout_id
     * @param string $dialout_number
     * @param array{
     *  priority: integer,
     *  call_timeout: integer
     * }|[] $options
     *
     * @return Model\FollowMeEntry
     */
    public function create_dialout($customer_id, $follow_me_id, $dialout_id, $dialout_number, $options = [])
    {
        Argument::isInteger($customer_id);
        Argument::isInteger($follow_me_id);
        Argument::isInteger($dialout_id);
        Argument::isFormat($dialout_number, '/\d+/');
        Argument::isParams($options);

        $query = $this->protocol->prepareQuery('/api/v1/management/customers/{customer_id}/follow_me/{follow_me_id}/entries/{dialout_id}/dialout', [
            '{customer_id}' => $customer_id,
            '{follow_me_id}' => $follow_me_id,
            '{dialout_id}' => $dialout_id
        ]);

        $record = $this->protocol->create(
            $query,
            [
                                              'dialout_number' => $dialout_number,
                                              'options' => $options
                                          ]
        );

        return $this->recordToModel($record, Model\FollowMeEntry::class, $this->default_options($customer_id));
    }

    /**
     * @param integer $customer_id
     * @param integer $follow_me_id
     * @param integer $user_id
     * @param array{
     *  priority: integer,
     *  call_timeout: integer
     * }|[] $options
     *
     * @return Model\FollowMeEntry
     */
    public function create_user($customer_id, $follow_me_id, $user_id, $options = [])
    {
        Argument::isInteger($customer_id);
        Argument::isInteger($follow_me_id);
        Argument::isInteger($user_id);
        Argument::isParams($options);

        $query = $this->protocol->prepareQuery('/api/v1/management/customers/{customer_id}/follow_me/{follow_me_id}/entries/{user_id}/user', [
            '{customer_id}' => $customer_id,
            '{follow_me_id}' => $follow_me_id,
            '{user_id}' => $user_id
        ]);

        $record = $this->protocol->create($query, ['options' => $options]);

        return $this->recordToModel($record, Model\FollowMeEntry::class, $this->default_options($customer_id));
    }

    /**
     * @param integer $customer_id
     * @param integer $follow_me_id
     * @param integer $sound_id
     * @param array{
     *  priority: integer,
     *  call_timeout: integer
     * }|[] $options
     *
     * @return Model\FollowMeEntry
     */
    public function create_sound($customer_id, $follow_me_id, $sound_id, $options = [])
    {
        Argument::isInteger($customer_id);
        Argument::isInteger($follow_me_id);
        Argument::isInteger($sound_id);
        Argument::isParams($options);

        $query = $this->protocol->prepareQuery('/api/v1/management/customers/{customer_id}/follow_me/{follow_me_id}/entries/{sound_id}/sound', [
            '{customer_id}' => $customer_id,
            '{follow_me_id}' => $follow_me_id,
            '{sound_id}' => $sound_id
        ]);

        $record = $this->protocol->create($query, ['options' => $options]);

        return $this->recordToModel($record, Model\FollowMeEntry::class, $this->default_options($customer_id));
    }

    /**
     * See **ClientCurlTest** for details.
     *
     * @param int $customer_id
     * @param int $follow_me_id
     * @param int $id
     *
     * @return void
     */
    public function delete($customer_id, $follow_me_id, $id)
    {
        Argument::isInteger($customer_id);
        Argument::isInteger($id);

        $query = $this->protocol->prepareQuery('/api/v1/management/customers/{customer_id}/follow_me/{follow_me_id}/entries/{id}', [
            '{customer_id}' => $customer_id,
            '{follow_me_id}' => $follow_me_id,
            '{id}' => $id
        ]);

        $this->protocol->delete($query);

        return;
    }

    /**
     * @param integer $customer_id
     * @param integer $follow_me_id
     * @param integer $ivr_menu_id
     * @param array{
     *  priority: integer,
     * }|[] $options
     *
     * @return Model\FollowMeEntry
     */
    public function create_ivr_menu($customer_id, $follow_me_id, $ivr_menu_id, $options = [])
    {
        Argument::isInteger($customer_id);
        Argument::isInteger($follow_me_id);
        Argument::isInteger($ivr_menu_id);
        Argument::isParams($options);

        $query = $this->protocol->prepareQuery('/api/v1/management/customers/{customer_id}/follow_me/{follow_me_id}/entries/{ivr_menu_id}/ivr_menu', [
            '{customer_id}' => $customer_id,
            '{follow_me_id}' => $follow_me_id,
            '{ivr_menu_id}' => $ivr_menu_id
        ]);

        $record = $this->protocol->create($query, ['options' => $options]);

        return $this->recordToModel($record, Model\FollowMeEntry::class, $this->default_options($customer_id));
    }

    /**
     * @param integer $customer_id
     * @param integer $follow_me_id
     * @param integer $user_id
     * @param array{
     *  priority: integer,
     *  call_timeout: integer
     * }|[] $options
     *
     * @return Model\FollowMeEntry
     */
    public function create_voicemail($customer_id, $follow_me_id, $user_id, $options = [])
    {
        Argument::isInteger($customer_id);
        Argument::isInteger($follow_me_id);
        Argument::isInteger($user_id);
        Argument::isParams($options);

        $query = $this->protocol->prepareQuery('/api/v1/management/customers/{customer_id}/follow_me/{follow_me_id}/entries/{user_id}/voicemail', [
            '{customer_id}' => $customer_id,
            '{follow_me_id}' => $follow_me_id,
            '{user_id}' => $user_id
        ]);

        $record = $this->protocol->create($query, ['options' => $options]);

        return $this->recordToModel($record, Model\FollowMeEntry::class, $this->default_options($customer_id));
    }

    /**
     * @param integer $customer_id
     * @param integer $follow_me_id
     * @param integer $destination_follow_me_id
     * @param array{
     *  priority: integer,
     *  call_timeout: integer
     * }|[] $options
     *
     * @return Model\FollowMeEntry
     */
    public function create_redirect($customer_id, $follow_me_id, $destination_follow_me_id, $options = [])
    {
        Argument::isInteger($customer_id);
        Argument::isInteger($follow_me_id);
        Argument::isInteger($destination_follow_me_id);
        Argument::isParams($options);

        $query = $this->protocol->prepareQuery('/api/v1/management/customers/{customer_id}/follow_me/{follow_me_id}/entries/{destination_follow_me_id}/redirect_follow_me', [
            '{customer_id}' => $customer_id,
            '{follow_me_id}' => $follow_me_id,
            '{destination_follow_me_id}' => $destination_follow_me_id
        ]);

        $record = $this->protocol->create($query, ['options' => $options]);

        return $this->recordToModel($record, Model\FollowMeEntry::class, $this->default_options($customer_id));
    }

    /**
     * @param array<string, mixed> $record
     * @param integer $customer_id
     */
    public function apply_transform(&$record, $customer_id): void
    {
        $record['customer_id'] = $customer_id;
        if (array_key_exists('dialout_number', $record))
            $record['dialout_number'] = implode(',', $record['dialout_number']);
    }

    private function default_options($customer_id)
    {
        return [
            'transform' => [
                [$this, 'apply_transform'],
                [$customer_id]
            ]
        ];
    }
}

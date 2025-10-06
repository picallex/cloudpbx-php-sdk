<?php

// Copyright 2021 Picallex Holding Group. All rights reserved.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>
// @author (2023) Matias Damian Gomez <matias@picallex.com>

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
     * @param integer $user_id
     * @param array<string,mixed> $params
     *
     * @return Model\IvrMenuEntry
     */
    public function create_user($customer_id, $ivr_menu_id, $user_id, $params)
    {
        Argument::isInteger($customer_id);
        Argument::isInteger($ivr_menu_id);
        Argument::isInteger($user_id);
        Argument::isParams($params);

        $query = $this->protocol->prepareQuery('/api/v1/management/customers/{customer_id}/ivr_menus/{ivr_menu_id}/entry/{user_id}/user', [
            '{customer_id}' => $customer_id,
            '{ivr_menu_id}' => $ivr_menu_id,
            '{user_id}' => $user_id
        ]);

        $record = $this->protocol->create($query, $params);

        return $this->toModel($customer_id, $record);
    }

    /**
     * @param integer $customer_id
     * @param integer $ivr_menu_id
     * @param array<string,mixed> $params
     *
     * @return Model\IvrMenuEntry
     */
    public function create_user_regex($customer_id, $ivr_menu_id, $params)
    {
        Argument::isInteger($customer_id);
        Argument::isInteger($ivr_menu_id);
        Argument::isParams($params);

        $query = $this->protocol->prepareQuery('/api/v1/management/customers/{customer_id}/ivr_menus/{ivr_menu_id}/entry/user_regex', [
            '{customer_id}' => $customer_id,
            '{ivr_menu_id}' => $ivr_menu_id
        ]);

        $record = $this->protocol->create($query, $params);

        return $this->toModel($customer_id, $record);
    }

    /**
     * @param integer $customer_id
     * @param integer $ivr_menu_id
     * @param integer $callcenter_queue_id
     * @param array<string,mixed> $params
     *
     * @return Model\IvrMenuEntry
     */
    public function create_callcenter_queue($customer_id, $ivr_menu_id, $callcenter_queue_id, $params)
    {
        Argument::isInteger($customer_id);
        Argument::isInteger($ivr_menu_id);
        Argument::isInteger($callcenter_queue_id);
        Argument::isParams($params);

        $query = $this->protocol->prepareQuery('/api/v1/management/customers/{customer_id}/ivr_menus/{ivr_menu_id}/entry/{callcenter_queue_id}/callcenter_queue', [
            '{customer_id}' => $customer_id,
            '{ivr_menu_id}' => $ivr_menu_id,
            '{callcenter_queue_id}' => $callcenter_queue_id
        ]);

        $record = $this->protocol->create($query, $params);

        return $this->toModel($customer_id, $record);
    }

    /**
     * @param integer $customer_id
     * @param integer $ivr_menu_id
     * @param integer $follow_me_id
     * @param array<string,mixed> $params
     *
     * @return Model\IvrMenuEntry
     */
    public function create_follow_me($customer_id, $ivr_menu_id, $follow_me_id, $params)
    {
        Argument::isInteger($customer_id);
        Argument::isInteger($ivr_menu_id);
        Argument::isInteger($follow_me_id);
        Argument::isParams($params);

        $query = $this->protocol->prepareQuery('/api/v1/management/customers/{customer_id}/ivr_menus/{ivr_menu_id}/entry/{follow_me_id}/follow_me', [
            '{customer_id}' => $customer_id,
            '{ivr_menu_id}' => $ivr_menu_id,
            '{follow_me_id}' => $follow_me_id
        ]);

        $record = $this->protocol->create($query, $params);

        return $this->toModel($customer_id, $record);
    }

    /**
     * @param integer $customer_id
     * @param integer $ivr_menu_id
     * @param integer $sound_id
     * @param array<string,mixed> $params
     *
     * @return Model\IvrMenuEntry
     */
    public function create_playback($customer_id, $ivr_menu_id, $sound_id, $params)
    {
        Argument::isInteger($customer_id);
        Argument::isInteger($ivr_menu_id);
        Argument::isInteger($sound_id);
        Argument::isParams($params);

        $query = $this->protocol->prepareQuery('/api/v1/management/customers/{customer_id}/ivr_menus/{ivr_menu_id}/entry/{sound_id}/playback', [
            '{customer_id}' => $customer_id,
            '{ivr_menu_id}' => $ivr_menu_id,
            '{sound_id}' => $sound_id
        ]);

        $record = $this->protocol->create($query, $params);

        return $this->toModel($customer_id, $record);
    }

    /**
     * @param integer $customer_id
     * @param integer $ivr_menu_id
     * @param integer $submenu_id
     * @param array<string,mixed> $params
     *
     * @return Model\IvrMenuEntry
     */
    public function create_submenu($customer_id, $ivr_menu_id, $submenu_id, $params)
    {
        Argument::isInteger($customer_id);
        Argument::isInteger($ivr_menu_id);
        Argument::isInteger($submenu_id);
        Argument::isParams($params);

        $query = $this->protocol->prepareQuery('/api/v1/management/customers/{customer_id}/ivr_menus/{ivr_menu_id}/entry/{submenu_id}/submenu', [
            '{customer_id}' => $customer_id,
            '{ivr_menu_id}' => $ivr_menu_id,
            '{submenu_id}' => $submenu_id
        ]);

        $record = $this->protocol->create($query, $params);

        return $this->toModel($customer_id, $record);
    }

    /**
     * @param integer $customer_id
     * @param integer $ivr_menu_id
     * @param array<string,mixed> $params
     *
     * @return Model\IvrMenuEntry
     */
    public function create_topmenu($customer_id, $ivr_menu_id, $params)
    {
        Argument::isInteger($customer_id);
        Argument::isInteger($ivr_menu_id);
        Argument::isParams($params);

        $query = $this->protocol->prepareQuery('/api/v1/management/customers/{customer_id}/ivr_menus/{ivr_menu_id}/entry/top', [
            '{customer_id}' => $customer_id,
            '{ivr_menu_id}' => $ivr_menu_id,
        ]);

        $record = $this->protocol->create($query, $params);

        return $this->toModel($customer_id, $record);
    }

    /**
     * @param integer $customer_id
     * @param integer $ivr_menu_id
     * @param integer $voicemail_id
     * @param array<string,mixed> $params
     *
     * @return Model\IvrMenuEntry
     */
    public function create_voicemail($customer_id, $ivr_menu_id, $voicemail_id, $params)
    {
        Argument::isInteger($customer_id);
        Argument::isInteger($ivr_menu_id);
        Argument::isInteger($voicemail_id);
        Argument::isParams($params);

        $query = $this->protocol->prepareQuery('/api/v1/management/customers/{customer_id}/ivr_menus/{ivr_menu_id}/entry/{voicemail_id}/voicemail', [
            '{customer_id}' => $customer_id,
            '{ivr_menu_id}' => $ivr_menu_id,
            '{voicemail_id}' => $voicemail_id
        ]);

        $record = $this->protocol->create($query, $params);

        return $this->toModel($customer_id, $record);
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

    /**
     * @param integer $customer_id
     * @param integer $ivr_menu_id
     * @param integer $id
     *
     * @return void
     */
    public function delete($customer_id, $ivr_menu_id, $id)
    {
        $query = $this->protocol->prepareQuery('/api/v1/management/customers/{customer_id}/ivr_menus/{ivrmenu_id}/entries/{id}', [
            '{customer_id}' => $customer_id,
            '{ivrmenu_id}' => $ivr_menu_id,
            '{id}' => $id
        ]);

        $this->protocol->delete($query);

        return;
    }

    private function toModel($customer_id, $record)
    {
        return $this->recordToModel(
            $record,
            Model\IvrMenuEntry::class,
            [
               'transform' => [
                   function (&$record, $customer_id) {
                       $record['customer_id'] = $customer_id;
                   },
                   [$customer_id]
               ]
            ]
        );
    }
}

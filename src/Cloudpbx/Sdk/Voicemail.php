<?php

// Copyright 2021 Picallex Holding Group. All rights reserved.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk;

use Cloudpbx\Util\Argument;

class Voicemail extends Api
{
    /**
     * @param int $customer_id
     * @return array<Model\Voicemail>
     */
    public function all($customer_id)
    {
        Argument::isInteger($customer_id);

        $query = $this->protocol->prepareQuery('/api/v1/management/customers/{customer_id}/voicemails', ['{customer_id}' => $customer_id]);

        $records = $this->protocol->list(
            $query
        );

        return $this->recordsToModel($records, Model\Voicemail::class);
    }

    /**
     * @param int $customer_id
     * @param int $id
     *
     * @return Model\Voicemail
     */
    public function show($customer_id, $id)
    {
        Argument::isInteger($customer_id);
        Argument::isInteger($id);

        $query = $this->protocol->prepareQuery('/api/v1/management/customers/{customer_id}/voicemails/{voicemail_id}', [
            '{customer_id}' => $customer_id,
            '{voicemail_id}' => $id
        ]);

        $record = $this->protocol->list(
            $query
        );

        return $this->recordToModel($record, Model\Voicemail::class);
    }

    /**
     * See **VoicemailTest** for details.
     *
     * @return Model\Voicemail
     */
    public function create(int $customer_id, int $user_id, string $description, string $mailto, array $extras = [])
    {
        Argument::isInteger($customer_id);
        Argument::isInteger($user_id);
        Argument::isString($description);
        Argument::isString($mailto);

        $query = $this->protocol->prepareQuery('/api/v1/management/customers/{customer_id}/users/{user_id}/voicemails',
        [
            '{customer_id}' => $customer_id,
            '{user_id}' => $user_id
        ]);

        $params = array_merge($extras, ['description' => $description, 'mailto' => $mailto]);
        $record = $this->protocol->create($query, ['voicemail' => $params]);

        return $this->recordToModel($record, Model\Voicemail::class);
    }

    /**
     * See **VoicemailTest** for details.
     *
     * @param int $customer_id
     * @param int $user_id
     * @param int $voicemail_id
     * @param array<string,mixed> $params
     *
     * @return Model\Voicemail
     */
    public function update(int $customer_id, int $user_id, int $voicemail_id, array $params = [])
    {
        Argument::isInteger($customer_id);
        Argument::isParams($params);

        $query = $this->protocol->prepareQuery('/api/v1/management/customers/{customer_id}/users/{user_id}/voicemails/{voicemail_id}',
        [
            '{customer_id}' => $customer_id,
            '{user_id}' => $user_id,
            '{voicemail_id}' => $voicemail_id
        ]);

        $record = $this->protocol->update($query, ['voicemail' => $params]);

        return $this->recordToModel($record, Model\Voicemail::class);
    }

    /**
     * See **VoicemailTest** for details.
     *
     * @param int $customer_id
     * @param int $user_id
     * @param int $voicemail_id
     *
     * return void
     */
    public function delete(int $customer_id, int $user_id, int $voicemail_id)
    {
        Argument::isInteger($customer_id);
        Argument::isInteger($user_id);
        Argument::isInteger($voicemail_id);

        $query = $this->protocol->prepareQuery('/api/v1/management/customers/{customer_id}/users/{user_id}/voicemails/{voicemail_id}',
        [
            '{customer_id}' => $customer_id,
            '{user_id}' => $user_id,
            '{voicemail_id}' => $voicemail_id
        ]);

        $this->protocol->delete($query);

        return;
    }
}

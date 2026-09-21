<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.
//
// @author (2026) Agustin Serra <agustin@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk;

/**
 * Administrative overview of vpbxs: each freeswitch with the customers it
 * hosts, load counts, and the most recent provisioning movements.
 * A single request to the aggregated myflexpbx endpoint.
 */
class Vpbx extends Api
{
    /**
     * @return Model\VpbxOverview
     */
    public function overview()
    {
        $query = $this->protocol->prepareQuery('/api/v1/management/vpbx/overview');

        $record = $this->protocol->one($query);

        return $this->recordToModel($record, Model\VpbxOverview::class);
    }
}

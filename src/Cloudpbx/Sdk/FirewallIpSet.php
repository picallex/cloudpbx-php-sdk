<?php

// Copyright 2021 Picallex Holding Group. All rights reserved.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk;

use Cloudpbx\Util\Argument;

final class FirewallIpSet extends Api
{
    /**
     *
     * @return array<\Cloudpbx\Sdk\Model\FirewallIpSet>
     */
    public function all()
    {
        $query = $this->protocol->prepareQuery('/api/v1/management/firewall/ipset');

        $records = $this->protocol->list(
            $query
        );

        return $this->recordsToModel($records, Model\FirewallIpSet::class);
    }
}

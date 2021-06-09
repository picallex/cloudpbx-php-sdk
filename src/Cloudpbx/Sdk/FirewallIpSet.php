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

    /**
     * @param string $cidr_block
     * @param string $status
     *
     * @return \Cloudpbx\Sdk\Model\FirewallIpSet
     */
    public function create($cidr_block, $status)
    {
        Argument::isString($cidr_block);
        Argument::isString($status);
        Argument::choice($status, ['whitelist', 'blacklist']);

        $query = $this->protocol->prepareQuery('/api/v1/management/firewall/ipset');

        $record = $this->protocol->create(
            $query,
            ['cidr_block' => $cidr_block,
             'status' => $status]
        );

        return $this->recordToModel($record, Model\FirewallIpSet::class);
    }

    /**
     * @param string $cidr_block
     *
     * @return void
     */
    public function delete($cidr_block)
    {
        Argument::isString($cidr_block);

        $query = $this->protocol->prepareQuery('/api/v1/management/firewall/ipset/{cidr_block}', ['{cidr_block}' => urlencode($cidr_block)]);

        $this->protocol->delete($query);
    }
}

<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk\Implementation;

use Cloudpbx\Util\Argument;

final class FirewallIpSet extends \Cloudpbx\Sdk\Api implements \Cloudpbx\Sdk\FirewallIpSet
{
    public function all()
    {
        $query = $this->protocol->prepareQuery('/api/v1/management/firewall/ipset');

        $records = $this->protocol->list(
            $query
        );

        return $this->recordsToModel($records, \Cloudpbx\Sdk\Model\FirewallIpSet::class);
    }

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

        return $this->recordToModel($record, \Cloudpbx\Sdk\Model\FirewallIpSet::class);
    }

    public function delete($cidr_block)
    {
        Argument::isString($cidr_block);

        $query = $this->protocol->prepareQuery('/api/v1/management/firewall/ipset/{cidr_block}', ['{cidr_block}' => urlencode($cidr_block)]);

        $this->protocol->delete($query);
    }
}

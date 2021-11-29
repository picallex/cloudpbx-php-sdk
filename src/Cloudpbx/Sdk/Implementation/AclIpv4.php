<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk\Implementation;

use Cloudpbx\Util\Argument;

final class AclIpv4 extends \Cloudpbx\Sdk\Api implements \Cloudpbx\Sdk\AclIpv4
{
    public function all($customer_id)
    {
        Argument::isInteger($customer_id);

        $query = $this->protocol->prepareQuery('/api/v1/management/customers/{customer_id}/acl_ipv4s', ['{customer_id}' => $customer_id]);

        $records = $this->protocol->list($query);

        return $this->recordsToModel($records, \Cloudpbx\Sdk\Model\AclIpv4::class);
    }

    public function create($customer_id, $cidr)
    {
        Argument::isInteger($customer_id);
        Argument::isString($cidr);

        $query = $this->protocol->prepareQuery('/api/v1/management/customers/{customer_id}/acl_ipv4s', ['{customer_id}' => $customer_id]);

        $record = $this->protocol->create($query, ['cidr' => $cidr]);

        return $this->recordToModel($record, \Cloudpbx\Sdk\Model\AclIpv4::class);
    }

    public function delete($customer_id, $cidr)
    {
        Argument::isInteger($customer_id);
        Argument::isString($cidr);

        $query = $this->protocol->prepareQuery('/api/v1/management/customers/{customer_id}/acl_ipv4s', ['{customer_id}' => $customer_id]);

        $this->protocol->delete($query, ['cidr' => $cidr]);

        return;
    }
}

<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.

declare(strict_types=1);

namespace Cloudpbx\Sdk;

use Cloudpbx\Util\Argument;

class Cdr extends \Cloudpbx\Sdk\Api
{
    /**
     * trace a call detail record by its recorduuid.
     *
     * @param string $recorduuid
     * @param int $customer_id
     *
     * @return \Cloudpbx\Sdk\Model\CdrTrace
     */
    public function trace($recorduuid, $customer_id)
    {
        Argument::isString($recorduuid);
        Argument::isInteger($customer_id);

        $query = $this->protocol->prepareQuery(
            '/api/v1/root/cdr/trace?recorduuid={recorduuid}&customer_id={customer_id}',
            [
                '{recorduuid}' => urlencode($recorduuid),
                '{customer_id}' => $customer_id
            ]
        );

        $record = $this->protocol->one($query);

        // keep query identifiers available even if the api does not echo them back
        $record = array_merge(
            ['recorduuid' => $recorduuid, 'customer_id' => $customer_id],
            is_array($record) ? $record : []
        );

        return new \Cloudpbx\Sdk\Model\CdrTrace($record);
    }
}

<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.

declare(strict_types=1);

namespace Cloudpbx\Sdk;

use Cloudpbx\Util\Argument;

class SmartCallerid extends \Cloudpbx\Sdk\Api
{
    /**
     * Dialouts con smart callerid activo, cada uno con su grupo de callerids,
     * el pool de numeros y la reputacion por numero.
     *
     * @param int|null $customer_id acota el reporte a un customer, null = cross-customer
     * @param bool $verbose incluye el detalle de reputacion por carrier en cada callerid
     *
     * @return array<Model\SmartCalleridDialout>
     */
    public function status($customer_id = null, $verbose = false)
    {
        Argument::optional($customer_id, 'isInteger');

        $path = '/api/v1/management/smart_callerid/status';
        $params = [];

        if ($customer_id !== null) {
            $path .= '?customer_id={customer_id}';
            $params['{customer_id}'] = $customer_id;
        }

        if ($verbose) {
            $path .= ($params === [] ? '?' : '&') . 'verbose=true';
        }

        $query = $this->protocol->prepareQuery($path, $params);

        // el meta ({dialouts, customers}) es derivable del data, asi que solo
        // se mapea data. ponytail: si hace falta meta, agregar un statusRaw().
        $records = $this->protocol->list($query);

        return $this->recordsToModel($records, Model\SmartCalleridDialout::class);
    }
}

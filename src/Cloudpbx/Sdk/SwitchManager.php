<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.
//
// @author (2026) Agustin Serra <agustin@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk;

use Cloudpbx\Util\Argument;

/**
 * Reporte global (cross-customer) de dialouts y a que gateways apuntan, con
 * weights. Respalda el panorama del Switch Manager sin crawlear customer x
 * dialout: un solo request al endpoint agregado de myflexpbx.
 */
class SwitchManager extends Api
{
    /**
     * Todos los dialouts con sus gateways y weights.
     *
     * @param int|null $customer_id acota el reporte a un customer, null = cross-customer
     *
     * @return array<Model\SwitchManagerDialout>
     */
    public function overview($customer_id = null)
    {
        Argument::optional($customer_id, 'isInteger');

        $path = '/api/v1/management/switch_manager/overview';
        $params = [];

        if ($customer_id !== null) {
            $path .= '?customer_id={customer_id}';
            $params['{customer_id}'] = $customer_id;
        }

        $query = $this->protocol->prepareQuery($path, $params);

        // el meta ({dialouts, customers}) es derivable del data, asi que solo
        // se mapea data. ponytail: si hace falta meta, agregar un overviewRaw().
        $records = $this->protocol->list($query);

        return $this->recordsToModel($records, Model\SwitchManagerDialout::class);
    }
}

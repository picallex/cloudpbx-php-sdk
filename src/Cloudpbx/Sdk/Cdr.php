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
     * @param string|null $from  RFC3339 UTC, filtra por start_at (opcional)
     * @param string|null $to    RFC3339 UTC, filtra por start_at (opcional)
     *
     * @throws \InvalidArgumentException si se pasa $to sin $from
     *
     * @return \Cloudpbx\Sdk\Model\CdrTrace
     */
    public function trace($recorduuid, $customer_id, $from = null, $to = null)
    {
        Argument::isString($recorduuid);
        Argument::isInteger($customer_id);

        // sin $from el backend lo completa con hoy-20dias, asi que un $to mas
        // viejo que eso da una ventana vacia y un 404 enganoso
        if ($to !== null && $from === null) {
            throw new \InvalidArgumentException('to requiere from');
        }

        $path = '/api/v1/root/cdr/trace?recorduuid={recorduuid}&customer_id={customer_id}';
        $params = [
            '{recorduuid}' => urlencode($recorduuid),
            '{customer_id}' => $customer_id
        ];

        // sin from/to el backend usa un lookback por defecto de 20 dias, o sea
        // no encuentra llamadas mas viejas que eso salvo que se pase from
        if ($from !== null) {
            Argument::isString($from);
            $path .= '&from={from}';
            $params['{from}'] = urlencode($from);
        }

        if ($to !== null) {
            Argument::isString($to);
            $path .= '&to={to}';
            $params['{to}'] = urlencode($to);
        }

        $query = $this->protocol->prepareQuery($path, $params);

        // este endpoint no envuelve la respuesta en {"data": ...}, viene en la raiz
        $record = $this->protocol->oneRaw($query);

        // keep query identifiers available even if the api does not echo them back
        $record = array_merge(
            ['recorduuid' => $recorduuid, 'customer_id' => $customer_id],
            is_array($record) ? $record : []
        );

        return new \Cloudpbx\Sdk\Model\CdrTrace($record);
    }
}

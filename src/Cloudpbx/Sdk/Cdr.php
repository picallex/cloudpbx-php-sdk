<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.

declare(strict_types=1);

namespace Cloudpbx\Sdk;

use Cloudpbx\Util\Argument;

class Cdr extends \Cloudpbx\Sdk\Api
{
    /**
     * Search recent call detail records for a customer within a time window.
     * Read-only. Returns the decoded body as-is: `{total, total_rows, rows}`,
     * where each row is a CDR map (nil/empty fields omitted by the api). Rows
     * are report data, not entity records, so they are not mapped to a Model.
     *
     * @param int $customer_id
     * @param string $from   RFC3339 UTC (required; backend default lookback is ~20 days)
     * @param string $to     RFC3339 UTC (required)
     * @param int $offset     pagination offset (default 0)
     * @param int $limit      page size (default 300, matches the api default)
     *
     * @return array<string, mixed>
     */
    public function search($customer_id, $from, $to, $offset = 0, $limit = 300)
    {
        Argument::isInteger($customer_id);
        Argument::isString($from);
        Argument::isString($to);
        Argument::isInteger($offset);
        Argument::isInteger($limit);

        $query = $this->protocol->prepareQuery(
            '/api/v1/root/cdr/search?customer_id={customer_id}&from={from}&to={to}&offset={offset}&limit={limit}',
            [
                '{customer_id}' => $customer_id,
                '{from}' => urlencode($from),
                '{to}' => urlencode($to),
                '{offset}' => $offset,
                '{limit}' => $limit,
            ]
        );

        // this endpoint returns the body at the root, without a {"data": ...} envelope
        return $this->protocol->oneRaw($query);
    }

    /**
     * Search recent recording CDRs for a customer within a time window, via the
     * optimized vip2phone endpoint (partition-pruned by from/to, read replica,
     * ordered by most recent). Each row includes a `playback_url`. Read-only.
     *
     * Prefer this over search() for a live "recent calls" view: it is the
     * recordings-oriented, optimized path (the rate limit lives on the actual
     * recording playback, not on this search).
     *
     * Returns the decoded body as-is: `{total, total_rows, rows}`.
     *
     * @param int $customer_id
     * @param string $from   RFC3339 UTC (required)
     * @param string $to     RFC3339 UTC (required)
     * @param int $offset     pagination offset (default 0)
     * @param int $limit      page size (default 500, matches the api default)
     *
     * @return array<string, mixed>
     */
    public function recordingSearch($customer_id, $from, $to, $offset = 0, $limit = 500)
    {
        Argument::isInteger($customer_id);
        Argument::isString($from);
        Argument::isString($to);
        Argument::isInteger($offset);
        Argument::isInteger($limit);

        $query = $this->protocol->prepareQuery(
            '/api/v1/management/vendor/vip2phone/cdr/recording?customer_id={customer_id}&from={from}&to={to}&offset={offset}&limit={limit}',
            [
                '{customer_id}' => $customer_id,
                '{from}' => urlencode($from),
                '{to}' => urlencode($to),
                '{offset}' => $offset,
                '{limit}' => $limit,
            ]
        );

        // rendered by CdrView, body at the root without a {"data": ...} envelope
        return $this->protocol->oneRaw($query);
    }

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

<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.

declare(strict_types=1);

namespace Cloudpbx\Sdk;

use Cloudpbx\Util\Argument;

class NumberLookup extends \Cloudpbx\Sdk\Api
{
    /**
     * List stored carrier rows (most recently queried first).
     *
     * Optional filters: `number`, `carrier` (substring match), `line_type`,
     * `region`, `provider` (exact), plus `limit`/`offset` for pagination.
     *
     * @param array<string, mixed> $params
     *
     * @return array<\Cloudpbx\Sdk\Model\NumberLookup>
     */
    public function all($params = [])
    {
        Argument::isParams($params);

        $url = '/api/v1/management/number_lookup';
        if (count($params) > 0) {
            $url .= '?' . http_build_query($params);
        }

        $query = $this->protocol->prepareQuery($url);
        $records = $this->protocol->list($query);

        return array_map(
            function ($record) {
                return new \Cloudpbx\Sdk\Model\NumberLookup($record);
            },
            $records
        );
    }

    /**
     * Resolve the carrier for a phone number on demand (cache -> DB -> provider).
     *
     * @param string $number E.164 (e.g. +14159929960)
     *
     * @return \Cloudpbx\Sdk\Model\NumberLookup
     */
    public function show($number)
    {
        Argument::isString($number);

        $query = $this->protocol->prepareQuery(
            '/api/v1/management/number_lookup/{number}',
            ['{number}' => urlencode($number)]
        );

        $record = $this->protocol->one($query);

        return new \Cloudpbx\Sdk\Model\NumberLookup($record);
    }
}

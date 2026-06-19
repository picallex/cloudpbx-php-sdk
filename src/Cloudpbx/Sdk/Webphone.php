<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.
//
// @author (2026) Agustin Serra <agustin@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk;

use Cloudpbx\Util\Argument;

final class Webphone extends Api
{
    /**
     * Get availability for a single extension.
     *
     * GET /api/v1/management/webphone/extensions/{extension}/availability?domain={domain}
     *
     * @param string $domain
     * @param string $extension
     *
     * @return Model\Webphone\ExtensionAvailability
     */
    public function getExtensionAvailability(string $domain, string $extension)
    {
        Argument::isString($domain);
        Argument::isString($extension);

        $qs = http_build_query(['domain' => $domain]);
        $query = $this->protocol->prepareQuery(
            '/api/v1/management/webphone/extensions/{extension}/availability?' . $qs,
            ['{extension}' => $extension]
        );

        $record = $this->protocol->one($query);

        return Model\Webphone\ExtensionAvailability::fromArray($record);
    }

    /**
     * List availability for extensions in a domain.
     *
     * GET /api/v1/management/webphone/extensions/availability?domain={domain}&q={q}&limit={limit}&offset={offset}&transferable={true|false}
     *
     * Required params:
     *   - domain (string)
     *
     * Optional params:
     *   - q (string)
     *   - limit (int)
     *   - offset (int)
     *   - transferable (bool)
     *
     * @param array<string, mixed> $params
     *
     * @return Model\Webphone\ExtensionAvailabilityList
     */
    public function listExtensionAvailability(array $params)
    {
        Argument::keyExists($params, 'domain');
        Argument::isString($params['domain']);

        $query_params = ['domain' => $params['domain']];

        if (isset($params['q'])) {
            Argument::isString($params['q']);
            $query_params['q'] = $params['q'];
        }

        if (isset($params['limit'])) {
            Argument::isInteger($params['limit']);
            $query_params['limit'] = $params['limit'];
        }

        if (isset($params['offset'])) {
            Argument::isInteger($params['offset']);
            $query_params['offset'] = $params['offset'];
        }

        if (array_key_exists('transferable', $params)) {
            if (!is_bool($params['transferable'])) {
                throw new \InvalidArgumentException('transferable must be a boolean');
            }
            $query_params['transferable'] = $params['transferable'] ? 'true' : 'false';
        }

        $qs = http_build_query($query_params);
        $query = $this->protocol->prepareQuery(
            '/api/v1/management/webphone/extensions/availability?' . $qs
        );

        $raw = $this->protocol->listRaw($query);

        $items = array_map(
            function ($item) {
                return Model\Webphone\ExtensionAvailability::fromArray($item);
            },
            $raw['data'] ?? []
        );

        return new Model\Webphone\ExtensionAvailabilityList(
            $items,
            $raw['pagination'] ?? [],
            $raw['summary'] ?? []
        );
    }
}

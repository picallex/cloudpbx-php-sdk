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
    public const STATUS_AVAILABLE = 'available';
    public const STATUS_BUSY = 'busy';

    private const STATUSES = [self::STATUS_AVAILABLE, self::STATUS_BUSY];

    /**
     * Get the agent status of a single extension.
     *
     * GET /api/v1/management/webphone/extensions/{extension}/agent-status?domain={domain}
     *
     * A diferencia de getExtensionAvailability, este endpoint falla en vez de
     * devolver un valor por defecto cuando no puede leer el runtime: informar
     * "sin pausa" ante un parpadeo haria que el llamador borre una pausa real.
     *
     * @param string $domain
     * @param string $extension
     *
     * @return Model\Webphone\AgentStatus
     */
    public function getAgentStatus(string $domain, string $extension)
    {
        Argument::isString($domain);
        Argument::isString($extension);

        $qs = http_build_query(['domain' => $domain]);
        $query = $this->protocol->prepareQuery(
            '/api/v1/management/webphone/extensions/{extension}/agent-status?' . $qs,
            ['{extension}' => $extension]
        );

        $record = $this->protocol->one($query);

        return Model\Webphone\AgentStatus::fromArray($record);
    }

    /**
     * Set the agent status of a single extension.
     *
     * PUT /api/v1/management/webphone/extensions/{extension}/agent-status
     *
     * Mismo efecto que marcar *78/*79, sin plan de marcado: no necesita un
     * registro sip vivo ni un canal.
     *
     * Required params:
     *   - status (string): 'available' | 'busy'
     *
     * Optional params:
     *   - substatus (string): nombre de la pausa, solo con status 'busy'. El
     *     catalogo de pausas vive en el consumidor, aca es texto libre. Sin
     *     substatus se conserva la pausa que el agente ya tenia.
     *
     * @param string $domain
     * @param string $extension
     * @param array<string, mixed> $params
     *
     * @return Model\Webphone\AgentStatus
     */
    public function setAgentStatus(string $domain, string $extension, array $params)
    {
        Argument::isString($domain);
        Argument::isString($extension);
        Argument::keyExists($params, 'status');
        Argument::isString($params['status']);
        Argument::choice($params['status'], self::STATUSES);

        $body = ['domain' => $domain, 'status' => $params['status']];

        if (isset($params['substatus'])) {
            Argument::isString($params['substatus']);

            if ($params['status'] !== self::STATUS_BUSY) {
                throw new \InvalidArgumentException('substatus only applies to status busy');
            }

            $body['substatus'] = $params['substatus'];
        }

        $query = $this->protocol->prepareQuery(
            '/api/v1/management/webphone/extensions/{extension}/agent-status',
            ['{extension}' => $extension]
        );

        $record = $this->protocol->update($query, $body);

        return Model\Webphone\AgentStatus::fromArray($record);
    }

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

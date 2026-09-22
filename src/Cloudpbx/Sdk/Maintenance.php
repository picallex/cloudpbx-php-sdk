<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.
//
// @author (2026) Agustin Serra <agustin@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk;

/**
 * Node-level maintenance/observability reads used to build a per-PBX support
 * dashboard: live FreeSWITCH status, SIP registrations, service health and
 * call-center agent status.
 *
 * The payloads carry raw FreeSWITCH console text (sofia_status, status) that
 * the caller parses; they are not entity records, so they are returned decoded
 * as-is instead of mapped to a Model.
 *
 * ponytail: no Model classes here on purpose — the shapes are node dumps with
 * embedded text blobs and dynamic node-keyed maps that model to nothing useful.
 */
class Maintenance extends Api
{
    /**
     * GET a maintenance endpoint and return the decoded body. These routes are
     * inconsistent about the `data` envelope (freeswitch/status wraps, service
     * and registrations do not), so unwrap it only when present.
     *
     * @param string $path
     * @return array<mixed>
     */
    private function read($path)
    {
        $body = $this->protocol->listRaw($this->protocol->prepareQuery($path));

        if (is_array($body) && array_key_exists('data', $body)) {
            return $body['data'];
        }

        return is_array($body) ? $body : [];
    }

    /**
     * Live FreeSWITCH status, one entry per node.
     *
     * Each entry has `name`/`hostname`, a structured `node` (alive, limits,
     * memory, version) and two raw console blobs: `status` (uptime, sessions,
     * CPS, CPU) and `sofia_status` (profiles and gateways per domain + state).
     * Filter `sofia_status` by domain to scope it to a single PBX.
     *
     * @return array<mixed>
     */
    public function freeswitchStatus()
    {
        return $this->read('/api/v1/maintenance/freeswitch/status');
    }

    /**
     * SIP registrations keyed by node name: `{ "node@ip": [ ...registrations ] }`.
     * Answers who is actually registered right now (empty list = nobody).
     *
     * @return array<string, mixed>
     */
    public function freeswitchRegistrations()
    {
        return $this->read('/api/v1/maintenance/freeswitch/status/registrations');
    }

    /**
     * Overall service health of the node.
     *
     * @return array<string, mixed>
     */
    public function serviceStatus()
    {
        return $this->read('/api/v1/maintenance/service/status');
    }

    /**
     * Live call-center agent status (Available / On Break / in call, ...).
     *
     * @return array<mixed>
     */
    public function callcenterAgentStatus()
    {
        return $this->read('/api/v1/maintenance/callcenter/agent/status');
    }
}

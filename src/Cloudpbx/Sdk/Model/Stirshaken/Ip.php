<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.
//
// @author (2026) Agustin Serra <agustin@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk\Model\Stirshaken;

final class Ip extends \Cloudpbx\Sdk\Model
{
    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $ip_cidr;

    /**
     * @var string|null
     */
    public $description;

    /**
     * @var string|null
     */
    public $certificate_id;

    /**
     * @var string|null
     */
    public $certificate_name;

    /**
     * @var string proxy|redirect
     */
    public $forwarding_mode;

    /**
     * @var string|null
     */
    public $destination_uri;

    /**
     * @var string outbound|inbound|both
     */
    public $call_direction;

    /**
     * @var int
     */
    public $cps_limit;

    /**
     * @var string|null
     */
    public $customer_prefix;

    /**
     * @var string|null
     */
    public $provider_prefix;

    /**
     * customer de cloudpbx (myflexpbx) que origino esta IP.
     *
     * @var int|null
     */
    public $cloudpbx_customer_id;

    /**
     * recurso origen: 'dialout_group:{dialout_id}:{group_id}' | 'dialout:{id}'.
     *
     * @var string|null
     */
    public $source_ref;

    /**
     * proveedores asignados (bulk): [{provider_id, provider_name, weight}, ...].
     *
     * @var array<int, array<string, mixed>>
     */
    public $providers = [];

    /**
     * @var bool
     */
    public $is_active;

    /**
     * @var string|null ISO-8601
     */
    public $created_at;

    public function __construct()
    {
    }
}

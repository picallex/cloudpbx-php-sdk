<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.
//
// @author (2026) Agustin Serra <agustin@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk\Model\Stirshaken;

final class Provider extends \Cloudpbx\Sdk\Model
{
    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $tenant_id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var int
     */
    public $priority;

    /**
     * @var int
     */
    public $weight;

    /**
     * @var string|null
     */
    public $destination_uri;

    /**
     * @var int
     */
    public $cps_limit;

    /**
     * @var string|null
     */
    public $provider_prefix;

    /**
     * @var bool
     */
    public $is_active;

    /**
     * @var string|null ISO-8601
     */
    public $created_at;

    /**
     * ips de destino del proveedor tal como las devuelve la api (array crudo).
     *
     * @var array<int, array<string, mixed>>
     */
    public $ips;

    public function __construct()
    {
    }
}

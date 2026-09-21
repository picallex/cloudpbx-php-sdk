<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.
//
// @author (2026) Agustin Serra <agustin@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk\Model;

final class VpbxFreeswitch extends \Cloudpbx\Sdk\Model
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string|null
     */
    public $switchname;

    /**
     * @var bool
     */
    public $multitenant;

    /**
     * @var bool
     */
    public $enabled_for_provisioning;

    /**
     * @var int|null
     */
    public $s3_concurrency;

    /**
     * @var int
     */
    public $customers_count;

    /**
     * @var int
     */
    public $extensions_count;

    /**
     * Customers hosted on this node. Kept as a documented raw array (same lazy
     * convention as SwitchManagerDialout::$gateways).
     *
     * @var array<int, array{
     *   id: int,
     *   name: string,
     *   domain: string|null,
     *   extensions_count: int,
     *   active: bool
     * }>
     */
    public $customers = [];

    public function __construct()
    {
    }
}

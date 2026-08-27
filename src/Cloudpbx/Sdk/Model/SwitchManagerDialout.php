<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.
//
// @author (2026) Agustin Serra <agustin@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk\Model;

final class SwitchManagerDialout extends \Cloudpbx\Sdk\Model
{
    /**
     * @var false el reporte no expone un id propio, la clave es dialout_id
     */
    public $id = false;

    /** @var int */
    public $customer_id;

    /** @var string|null */
    public $customer_name;

    /** @var int */
    public $dialout_id;

    /** @var string */
    public $dialout_name;

    /** @var string|null */
    public $destination;

    /** @var string|null */
    public $gateway_strategy;

    /**
     * gateways del dialout con su weight. El "primario" se calcula sobre estos
     * weights + gateway_strategy en el consumidor.
     *
     * @var array<int, array{
     *   id: int,
     *   gateway_id: int,
     *   gateway_name: string|null,
     *   realm: string|null,
     *   weight: int|null,
     *   strip: string|null,
     *   prepend: string|null,
     *   try_when_fail_with: string|null
     * }>
     */
    public $gateways = [];

    public function __construct()
    {
    }
}

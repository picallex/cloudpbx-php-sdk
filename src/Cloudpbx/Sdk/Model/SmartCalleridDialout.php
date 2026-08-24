<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.

declare(strict_types=1);

namespace Cloudpbx\Sdk\Model;

final class SmartCalleridDialout extends \Cloudpbx\Sdk\Model
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

    /** @var string */
    public $callerid_strategy;

    /** @var bool */
    public $smart_callerid_enabled;

    /** @var int|null */
    public $callerid_group_id;

    /** @var string|null */
    public $callerid_group_name;

    /**
     * pool de callerids del grupo. `reputation` viene solo con verbose=true.
     *
     * @var array<int, array{
     *   number: string,
     *   name: string,
     *   status: string,
     *   spam_carriers: array<int, string>,
     *   reputation?: array<int, array{carrier: string, status: string, score: float|null, source: string|null, checked_at: string|null}>
     * }>
     */
    public $callerids = [];

    public function __construct()
    {
    }
}

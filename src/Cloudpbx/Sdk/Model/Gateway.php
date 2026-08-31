<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.
//
// @author (2026) Agustin Serra <agustin@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk\Model;

/**
 * Entrada del catalogo global de gateways (root). Minima a proposito:
 * fromArray() ignora claves extra del payload root.
 */
final class Gateway extends \Cloudpbx\Sdk\Model
{
    /**
     * @var integer
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $realm;

    /**
     * @var integer
     */
    public $endpoint_id;

    public function __construct()
    {
    }
}

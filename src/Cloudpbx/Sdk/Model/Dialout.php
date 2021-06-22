<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk\Model;

final class Dialout extends \Cloudpbx\Sdk\Model
{
    /**
     * @var integer
     */
    public $id;

    /**
     * @var integer
     */
    public $customer_id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $gateway_strategy;

    /**
     * @var string
     */
    public $callerid_strategy;

    /**
     * @var string
     */
    public $destination;

    /**
     * @var string
     */
    public $prepend;

    /**
     * @var string
     */
    public $strip;

    /**
     * @var integer
     */
    public $weight;

    public function __construct()
    {
    }
}

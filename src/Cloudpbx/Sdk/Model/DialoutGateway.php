<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.
//
// @author (2026) Agustin Serra <agustin@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk\Model;

final class DialoutGateway extends \Cloudpbx\Sdk\Model
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
     * @var integer
     */
    public $dialout_id;

    /**
     * @var integer
     */
    public $gateway_id;

    /**
     * @var string
     */
    public $strip;

    /**
     * @var string
     */
    public $prepend;

    public function __construct()
    {
    }
}

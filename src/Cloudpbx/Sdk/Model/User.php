<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk\Model;

final class User extends \Cloudpbx\Sdk\Model
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
     * @var Relation
     */
    public $customer;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $alias;

    /**
     * @var string
     */
    public $accountcode;

    /**
     * @var string
     */
    public $caller_name;

    /**
     * @var string
     */
    public $caller_number;

    /**
     * @var boolean
     */
    public $is_webrtc;

    /**
     * @var boolean
     */
    public $dnd_on_sip_unregister;

    /**
     * @var boolean
     */
    public $available_on_sip_register;

    /**
     * @var boolean
     */
    public $enable_outbound_fakering;

    public function __construct()
    {
    }

    protected function setup()
    {
        $this->customer = new Relation('customer', $this->customer_id);
    }
}

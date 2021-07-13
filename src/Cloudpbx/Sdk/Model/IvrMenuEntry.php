<?php

// Copyright 2021 Picallex Holding Group. All rights reserved.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk\Model;

final class IvrMenuEntry extends \Cloudpbx\Sdk\Model
{
    /**
     * @var integer
     */
    public $id;

    /**
     * @var integer
     */
    public $ivr_menu_id;

    /**
     * @var Relation
     */
    public $ivr_menu;

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
    public $digits;

    /**
     * @var string
     */
    public $action;

    /**
     * @var string
     */
    public $param;

    public function __construct()
    {
    }

    public function setup()
    {
        $this->ivr_menu = new Relation('ivr_menu', $this->ivr_menu_id);
        $this->customer = new Relation('customer', $this->customer_id);
    }
}

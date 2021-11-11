<?php

// Copyright 2021 Picallex Holding Group. All rights reserved.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk\Model\Customer;

final class Capability extends \Cloudpbx\Sdk\Model
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
     * @var \Cloudpbx\Sdk\Model\Relation
     */
    public $customer;

    /**
     * @var string
     */
    public $capability;

    /**
     * @var boolean
     */
    public $allowed;

    public function __construct()
    {
    }

    protected function setup()
    {
        $this->customer = new \Cloudpbx\Sdk\Model\Relation('customer', $this->customer_id);
    }
}

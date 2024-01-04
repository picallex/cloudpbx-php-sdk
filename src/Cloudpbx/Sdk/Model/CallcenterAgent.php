<?php

// Copyright 2021 Picallex Holding Group. All rights reserved.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk\Model;

final class CallcenterAgent extends \Cloudpbx\Sdk\Model
{
    /**
     * @var integer
     */
    public $id;

    /**
     * @var integer
     */
    public $user_id;

    /**
     * @var Relation
     */
    public $user;

    /**
     * @var integer
     */
    public $customer_id;

    /**
     * @var Relation
     */
    public $customer;

    /**
     * @var boolean
     */
    public $autologin;

    /**
     * @var integer
     */
    public $ring_timeout;

    public function __construct()
    {
    }

    protected function setup()
    {
        $this->user = new Relation('user', $this->user_id, [$this->customer_id]);
        $this->customer = new Relation('customer', $this->customer_id);
    }
}

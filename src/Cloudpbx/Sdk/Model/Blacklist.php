<?php

// Copyright 2021 Picallex Holding Group. All rights reserved.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk\Model;

final class Blacklist extends \Cloudpbx\Sdk\Model
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
    public $number;

    public function __construct()
    {
    }

    protected function setup()
    {
        $this->customer = new Relation('customer', $this->id, [$this->customer_id]);
    }
}

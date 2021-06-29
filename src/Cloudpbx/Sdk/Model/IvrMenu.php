<?php

// Copyright 2021 Picallex Holding Group. All rights reserved.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk\Model;

final class IvrMenu extends \Cloudpbx\Sdk\Model
{
    /**
     * @var integer
     */
    public $id;

    /**
     * @var integer
     * @deprecated use $customer relation
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
    public $description;


    public function __construct()
    {
    }

    public function setup()
    {
        $this->customer = new Relation('customer', $this->customer_id);
    }
}

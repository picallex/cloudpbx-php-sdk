<?php

// Copyright 2021 Picallex Holding Group. All rights reserved.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk\Model;

final class Group extends \Cloudpbx\Sdk\Model
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
    public $alias;

    /**
     * @var integer
     */
    public $customer_id;

    /**
     * @var Relation
     */
    public $customer;

    /**
     * @var array<mixed>
     */
    public $users = [];

    /**
     * @var array<Relation>
     */
    public $users_relation = [];

    public function __construct()
    {
    }

    protected function setup()
    {
        $this->customer = new Relation('customer', $this->customer_id);

        $this->users_relation = array_map(function (array $user_data) {
            return new Relation('user', $user_data['id'], [$this->customer_id]);
        }, $this->users);
    }
}

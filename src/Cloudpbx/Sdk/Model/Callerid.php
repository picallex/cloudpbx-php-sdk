<?php

// Copyright 2021 Picallex Holding Group. All rights reserved.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk\Model;

final class Callerid extends \Cloudpbx\Sdk\Model
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
    public $number;

    /**
     * @var integer
     */
    public $weight;

    /**
     * @var integer
     */
    public $callerid_group_id;

    /**
     * @var Relation
     */
    public $callerid_group;

    public function __construct()
    {
    }

    protected function setup()
    {
        $this->callerid_group = new Relation('callerid_group', $this->callerid_group_id);
    }
}

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

    /**
     * @var ?integer
     */
    public $callerid_group_id = null;

    /**
     * @var ?Relation
     */
    public $callerid_group = null;

    public function __construct()
    {
    }

    protected function setup()
    {
        $this->customer = new Relation('customer', $this->customer_id);

        if (is_numeric($this->callerid_group_id)) {
            $this->callerid_group = new Relation('callerid_group', $this->callerid_group_id, [$this->customer_id]);
        }
    }
}

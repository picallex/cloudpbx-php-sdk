<?php

/**
 * Copyright 2022 Picallex Holding Group. All rights reserved.
 *
 * @author (2022) Jovany Leandro G.C <jovany@picallex.com>
 */

declare(strict_types=1);

namespace Cloudpbx\Sdk\Model;

final class DialoutGroup extends \Cloudpbx\Sdk\Model
{
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
    public $callerid_group_id;

    /**
     * @var string
     */
    public $strip;

    /**
     * @var string
     */
    public $prepend;

    /**
     * @var string
     */
    public $pin;

    /**
     * @var Relation
     */
    public $callerid_group;

    /**
     * @var integer
     */
    public $group_id;

    /**
     * @var integer
     */
    public $minimal_call_duration_ms;

    /**
     * @var Relation
     */
    public $group;

    public function __construct()
    {
        $this->_primary_key = null;
    }

    protected function setup()
    {
        $this->callerid_group = new Relation('callerid_group', $this->callerid_group_id);
        $this->group = new Relation('group', $this->group_id);
    }
}

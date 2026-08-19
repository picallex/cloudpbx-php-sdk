<?php

// Copyright 2026 Picallex Holding Group. All rights reserved.

declare(strict_types=1);

namespace Cloudpbx\Sdk\Model;

final class UserCallerid extends \Cloudpbx\Sdk\Model
{
    /**
     * @var false record without primary key
     */
    public $id = false;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $number;

    /**
     * @var integer calls made with this callerid in the server-side window (last 24 hours)
     */
    public $calls;

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

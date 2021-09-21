<?php

// Copyright 2021 Picallex Holding Group. All rights reserved.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk\Model;

final class CallcenterTier extends \Cloudpbx\Sdk\Model
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
     * @var integer
     */
    public $callcenter_queue_id;

    /**
     * @var Relation
     */
    public $callcenter_queue;

    /**
     * @var integer
     */
    public $callcenter_agent_id;

    /**
     * @var Relation
     */
    public $callcenter_agent;

    public function __construct()
    {
    }

    protected function setup()
    {
        $this->customer = new Relation('customer', $this->customer_id);
        $this->callcenter_queue = new Relation('callcenter_queue', $this->callcenter_queue_id, [$this->customer_id]);
        $this->callcenter_agent = new Relation('callcenter_agent', $this->callcenter_agent_id, [$this->customer_id]);
    }
}

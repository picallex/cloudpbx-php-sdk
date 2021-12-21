<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk\Model;

final class CallcenterQueue extends \Cloudpbx\Sdk\Model
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
     * @var string|null
     */
    public $alias;

    /**
     * @var string
     */
    public $description;

    /**
     * @var string
     */
    public $strategy;

    /**
     * @var int|null
     */
    public $moh_sound_id;

    /**
     * @var Relation|null
     */
    public $moh_sound;

    /**
     * @var int
     */
    public $max_wait_time;

    /**
     * @var int
     */
    public $max_wait_time_with_no_agent;

    /**
     * @var int
     */
    public $max_wait_time_with_no_agent_time_reached;

    /**
     * @var int
     */
    public $discard_abandoned_after;

    /**
     * @var int
     */
    public $skip_agent_with_external_calls;

    public function __construct()
    {
    }

    protected function setup()
    {
        $this->customer = new Relation('customer', $this->customer_id);

        if ($this->moh_sound_id) {
            $this->moh_sound = new Relation('sound', $this->customer_id, [$this->moh_sound_id]);
        }
    }
}

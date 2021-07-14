<?php

// Copyright 2021 Picallex Holding Group. All rights reserved.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk\Model;

final class FollowMeEntry extends \Cloudpbx\Sdk\Model
{
    /**
     * @var integer
     */
    public $id;

    /**
     * @internal
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
    public $follow_me_id;

    /**
     * @var Relation
     */
    public $follow_me;

    /**
     * @var integer
     */
    public $priority;

    /**
     * @var string
     */
    public $time_of_day;

    /**
     * @var integer
     */
    public $call_timeout;

    /**
     * @internal use $belongs_to instead
     * @var integer
     */
    public $sound_id = null;

    /**
     * @internal use $belongs_to instead
     * @var integer
     */
    public $callcenter_queue_id = null;

    /**
     * @internal use $belongs_to instead
     * @var integer
     */
    public $voicemail_id = null;

    /**
     * @internal use $belongs_to instead
     * @var integer
     */
    public $dialout_id = null;

    /**
     * @internal use $belongs_to instead
     * @var integer
     */
    public $user_id = null;

    /**
     * @var Relation
     *
     * @see \Cloudpbx\Sdk\Client::preload for loading this relation as model
     */
    public $belongs_to;

    public function __construct()
    {
    }

    protected function setup()
    {
        $this->follow_me = new Relation('follow_me', $this->follow_me_id, [$this->customer_id]);
        $this->customer = new Relation('customer', $this->customer_id);

        $this->belongs_to = Relation::fromDescriptor(
            [
                'user' => $this->user_id,
                'dialout' => $this->dialout_id,
                'callcenter_queue' => $this->callcenter_queue_id,
                'follow_me' => $this->follow_me_id
            ],
            [$this->customer_id]
        );
    }
}

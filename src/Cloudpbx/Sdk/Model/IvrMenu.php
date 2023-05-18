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

    /**
     * @var integer
     */
    public $timeout;

    /**
     * @var integer
     */
    public $inter_digit_timeout;

    /**
     * @var integer
     */
    public $max_failures;

    /**
     * @var string
     */
    public $digit_len;

    /**
     * @var integer
     */
    public $greet_long_sound_id;

    /**
     * @var Relation | null
     */
    public $greet_long_sound = null;

    /**
     * @var integer
     */
    public $greet_short_sound_id;

    /**
     * @var Relation | null
     */
    public $greet_short_sound = null;

    /**
     * @var integer
     */
    public $invalid_sound_id;

    /**
     * @var Relation | null
     */
    public $invalid_sound = null;

    /**
     * @var integer
     */
    public $exit_sound_id;

    /**
     * @var Relation | null
     */
    public $exit_sound = null;

    public function __construct()
    {
    }

    public function setup()
    {
        $this->customer = new Relation('customer', $this->customer_id);
        $sounds_attr = ['greet_long_sound', 'greet_short_sound', 'invalid_sound', 'exit_sound'];
        foreach ($sounds_attr as $sound_attr) {
            $sound_attr_id = "${sound_attr}_id";
            if ($this->{$sound_attr_id}) {
                $this->{$sound_attr} = new Relation('sound', $this->{$sound_attr_id}, [$this->customer_id]);
            }
        }
    }
}

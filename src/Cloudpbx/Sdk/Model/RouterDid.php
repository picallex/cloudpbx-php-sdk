<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk\Model;

final class RouterDid extends \Cloudpbx\Sdk\Model
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
     * @var string
     */
    public $did;

    /**
     * @var integer|null
     * @deprecated since version v0.0.2 use $has_one instead
     */
    public $callcenter_queue_id = null;

    /**
     * @var integer|null
     * @deprecated since version v0.0.2 use $has_one instead
     */
    public $user_id = null;

    /**
     * @var integer|null
     * @deprecated since version v0.0.2 use $has_one instead
     */
    public $ivr_menu_id = null;

    /**
     * @var integer|null
     * @deprecated since version v0.0.2 use $has_one instead
     */
    public $dialout_id = null;

    /**
     * @var integer|null
     * @deprecated since version v0.0.2 use $has_one instead
     */
    public $follow_me_id = null;

    /**
     * @var Relation
     */
    public $has_one;

    public function __construct()
    {
    }

    public function setup()
    {
        $relations = [
            'user',
            'ivr_menu',
            'dialout',
            'callcenter_queue',
            'follow_me'
        ];

        foreach ($relations as $relation) {
            $relation_field = "{$relation}_id";
            $relation_id = $this->$relation_field;
            if (!is_null($relation_id)) {
                // ATTENTION(bit4bit): now this works because al relations only belongs to customer
                $this->has_one = new Relation($relation, $relation_id, [$this->customer_id]);
                break;
            }
        }

        //@phpstan-ignore-next-line
        if (is_null($this->has_one)) {
            throw new \RuntimeException('not found a relation for has_one');
        }
    }
}

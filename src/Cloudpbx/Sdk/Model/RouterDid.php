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
     * @var string
     */
    public $did;

    /**
     * @var integer|null
     */
    public $callcenter_queue_id = null;

    /**
     * @var integer|null
     */
    public $user_id = null;

    /**
     * @var integer|null
     */
    public $ivr_menu_id = null;

    /**
     * @var integer|null
     */
    public $dialout_id = null;

    public function __construct()
    {
    }
}

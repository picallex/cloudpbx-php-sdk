<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk\Model;

final class Customer extends \Cloudpbx\Sdk\Model
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
    public $domain;

    /**
     * @var int
     */
    public $limit_external_calls;

    /**
     * @var int
     */
    public $limit_inbound_external_calls;

    /**
     * @var string
     */
    public $accountcode;

    public function __construct()
    {
    }
}

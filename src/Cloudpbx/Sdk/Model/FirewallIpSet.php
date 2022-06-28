<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk\Model;

final class FirewallIpSet extends \Cloudpbx\Sdk\Model
{

    /**
     * @var string
     */
    public $cidr_block;

    /**
     * @var string
     */
    public $status;

    public function __construct()
    {
        $this->_primary_key = 'cidr_block';
    }
}

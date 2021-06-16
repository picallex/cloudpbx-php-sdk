<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk\Model;

final class FirewallIpSet extends \Cloudpbx\Sdk\Model
{
    protected static $_primary_key = 'cidr_block';

    public function __construct()
    {
    }
}

<?php

// Copyright 2021 Picallex Holding Group. All rights reserved.
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

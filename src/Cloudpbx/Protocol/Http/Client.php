<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Protocol\Http;

use Cloudpbx\Protocol\Http\Request;
use Cloudpbx\Protocol\Http\Response;

interface Client
{
    public function sendRequest(Request $request): Response;
}

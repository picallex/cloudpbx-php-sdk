<?php

// Copyright 2021 Picallex Holding Group. All rights reserved.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Http;

use Cloudpbx\Http\Request;
use Cloudpbx\Http\Response;

interface Client
{
    public function sendRequest(Request $request): Response;
}

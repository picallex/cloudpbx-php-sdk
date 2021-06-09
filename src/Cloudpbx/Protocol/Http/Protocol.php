<?php

// Copyright 2021 Picallex Holding Group. All rights reserved.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Protocol\Http;

interface Protocol
{
    /**
     * @param array<string, mixed> $current
     *
     * @return array<string, mixed>
     */
    public function setHeaders($current);

    /**
     * @param mixed $body
     * @param int $http_code
     *
     * @return Response
     */
    public function buildResponse($body, $http_code);
}

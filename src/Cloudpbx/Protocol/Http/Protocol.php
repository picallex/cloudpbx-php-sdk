<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.
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

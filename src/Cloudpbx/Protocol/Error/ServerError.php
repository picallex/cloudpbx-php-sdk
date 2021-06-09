<?php

// Copyright 2021 Picallex Holding Group. All rights reserved.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Protocol\Error;

final class ServerError extends \Exception
{
    /**
     * @var int
     */
    private $http_code;

    /**
     * @param int $http_code
     */
    public function __construct($http_code)
    {
        $this->http_code = $http_code;
    }

    public function __toString()
    {
        return __CLASS__ ." : server error {$this->http_code}";
    }
}

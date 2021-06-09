<?php

// Copyright 2021 Picallex Holding Group. All rights reserved.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Protocol\Error;

final class RequestError extends \Exception
{
    /**
     * @var int $http_code
     */
    private $http_code;

    /**
     * @param int $http_code
     */
    public function __construct($http_code)
    {
        $this->http_code = $http_code;
        $this->message = (string)$this;
    }

    public function __toString()
    {
        return __CLASS__ ." : request with http code {$this->http_code} error please check arguments";
    }
}

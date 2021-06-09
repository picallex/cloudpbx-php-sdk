<?php

// Copyright 2021 Picallex Holding Group. All rights reserved.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Protocol\Error;

final class RequestError extends ProtocolError
{
    /**
     * @var int $http_code
     */
    private $http_code;

    /**
     * @var string | null
     */
    private $response;

    /**
     * @param int $http_code
     * @param string | null $response
     */
    public function __construct($http_code, $response)
    {
        $this->http_code = $http_code;
        $this->response = $response;

        $this->message = (string)$this;
    }

    public function __toString()
    {
        return __CLASS__ ." : request with http code {$this->http_code} error please check arguments {$this->response}";
    }
}

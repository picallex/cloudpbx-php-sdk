<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Protocol\Error;

final class ServerError extends ProtocolError
{
    /**
     * @var int
     */
    private $http_code;

    /**
     * @var string
     */
    private $response;

    /**
     * @param int $http_code
     * @param string $response
     */
    public function __construct($http_code, $response)
    {
        $this->http_code = $http_code;
        $this->response = $response;
        $this->message = (string)$this;
    }

    public function __toString()
    {
        return __CLASS__ ." : server error {$this->http_code} has {$this->response} ";
    }
}

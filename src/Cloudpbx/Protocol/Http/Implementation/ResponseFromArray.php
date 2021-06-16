<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Protocol\Http\Implementation;

use Cloudpbx\Util;

class ResponseFromArray implements \Cloudpbx\Protocol\Http\Response
{
    /**
     * @var string
     */
    private $body;

    /**
     * @var int
     */
    private $http_code;

    /**
     * @param string $body
     * @param int $http_code
     */
    public function __construct($body, $http_code)
    {
        Util\Argument::isString($body);
        Util\Argument::isInteger($http_code);

        $this->body = $body;
        $this->http_code = $http_code;
    }

    /**
     * @return int
     */
    public function statusCode()
    {
        return $this->http_code;
    }

    /**
     * @return mixed
     */
    public function body()
    {
        return $this->body;
    }
}

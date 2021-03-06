<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Protocol\Http\Implementation;

use Cloudpbx\Util;

class RequestFromArray implements \Cloudpbx\Protocol\Http\Request
{
    /**
     * @var string
     */
    private $body;

    /**
     * @var string
     */
    private $url;

    /**
     * @var array<string, mixed>
     */
    private $headers = [];

    /**
     * @var string
     */
    private $method;

    /**
     * @param string $method
     * @param array<string, mixed> $value
     *
     * @return \Cloudpbx\Protocol\Http\Request
     */
    public static function build($method, $value)
    {
        Util\Argument::keyWithValue($value, 'headers');
        Util\Argument::keyWithValue($value, 'url');
        Util\Argument::choice($method, ['GET', 'POST', 'PUT', 'DELETE']);

        $obj = new self();
        $obj->body = $value['body'];
        $obj->url = $value['url'];
        $obj->headers = $value['headers'];
        $obj->method = $method;

        return $obj;
    }

    /**
     * @return null|string
     */
    public function body()
    {
        return $this->body;
    }

    /**
     * @return array<string, mixed>
     */
    public function headers()
    {
        return $this->headers;
    }

    /**
     * @return string
     */
    public function method()
    {
        return $this->method;
    }

    /**
     * @return string
     */
    public function url()
    {
        return $this->url;
    }
}

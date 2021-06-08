<?php

// Copyright 2021 Picallex Holding Group. All rights reserved.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Http\Implementation;

use Cloudpbx\Sdk\Util;

class RequestFromArray implements \Cloudpbx\Http\Request
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
     * @return \Cloudpbx\Http\Request
     */
    public static function build($method, $value)
    {
        Util\Argument::keyExists($value, 'body');
        Util\Argument::keyExists($value, 'headers');
        Util\Argument::keyExists($value, 'url');
        Util\Argument::choice($method, ['GET', 'POST', 'PUT', 'DELETE']);

        $obj = new self();
        $obj->body = $value['body'];
        $obj->url = $value['url'];
        $obj->headers = $value['headers'];
        $obj->method = $method;

        return $obj;
    }

    /**
     * @return mixed
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

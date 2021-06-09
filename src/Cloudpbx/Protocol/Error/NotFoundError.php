<?php

// Copyright 2021 Picallex Holding Group. All rights reserved.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Protocol\Error;

final class NotFoundError extends ProtocolError
{
    /**
     * @var string
     */
    private $resource;

    /**
     * @param string $resource
     */
    public function __construct($resource)
    {
        $this->resource = $resource;
    }

    public function __toString()
    {
        return __CLASS__ ." : resource {$this->resource} not found";
    }
}

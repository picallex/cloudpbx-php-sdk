<?php

// Copyright 2021 Picallex Holding Group. All rights reserved.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk;

interface Protocol
{
    /**
     * @param string $url
     * @param array<mixed> $params
     * @return string
     */
    public function prepareQuery($url, $params = []);

    /**
     * @param string $query
     * @return Array<mixed>
     */
    public function list($query);

    /**
     * @param string $query
     * @return mixed
     */
    public function one($query);
}

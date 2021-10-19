<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.
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

    /**
     * @param string $query
     * @param array<string, mixed> $params
     * @return mixed
     */
    public function create($query, $params);

    /**
     * @param string $query
     * @param array<string, mixed> $params
     * @return mixed
     */
    public function update($query, $params);

    /**
     * @param string $query
     * @param array<string, mixed>|null $params
     *
     * @return void
     */
    public function delete($query, $params = null);
}

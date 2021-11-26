<?php

// Copyright 2021 Picallex Holding Group. All rights reserved.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk;

interface FirewallIpSet
{
    /**
     *
     * @return array<\Cloudpbx\Sdk\Model\FirewallIpSet>
     */
    public function all();

    /**
     * @param string $cidr_block
     * @param string $status
     *
     * @return \Cloudpbx\Sdk\Model\FirewallIpSet
     */
    public function create($cidr_block, $status);

    /**
     * @param string $cidr_block
     *
     * @throws \Cloudpbx\Protocol\Error\NotFoundError cuando no se encuentra el recurso
     * @return void
     */
    public function delete($cidr_block);
}

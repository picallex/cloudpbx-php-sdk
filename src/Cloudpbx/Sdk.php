<?php

// Copyright 2021 Picallex Holding Group. All rights reserved.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx;

final class Sdk
{
    /**
     * Create a default implementation of client.
     *
     * @param string $api_base
     * @param string $api_key
     *
     * @return \Cloudpbx\Sdk\Client
     */
    public static function createDefaultClient($api_base, $api_key)
    {
        Util\Argument::isString($api_base);
        Util\Argument::isString($api_key);

        $protocol = Protocol\ProtocolHTTP::createWithDefaultClient($api_base, $api_key);

        $client =  new Sdk\Client($protocol);

        return $client;
    }
}

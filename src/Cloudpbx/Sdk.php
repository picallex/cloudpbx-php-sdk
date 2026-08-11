<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.
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

        $client =  new Sdk\Implementation\Client($protocol);

        return $client;
    }

    /**
     * Create a client for the stir/shaken platform (ClearIP Cloud).
     *
     * Es un backend distinto al de cloudpbx: otra base_url y otra api_key,
     * por eso se instancia su propio protocol y cliente.
     *
     * @param string $api_base
     * @param string $api_key
     *
     * @return \Cloudpbx\Sdk\Stirshaken\Client
     */
    public static function createStirshakenClient($api_base, $api_key)
    {
        Util\Argument::isString($api_base);
        Util\Argument::isString($api_key);

        $protocol = Protocol\ProtocolHTTP::createWithDefaultClient($api_base, $api_key);

        return new Sdk\Stirshaken\Implementation\Client($protocol);
    }
}

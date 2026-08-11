<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.
//
// @author (2026) Agustin Serra <agustin@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk\Stirshaken\Implementation;

use Cloudpbx\Sdk\Stirshaken\Ip;
use Cloudpbx\Sdk\Stirshaken\Certificate;
use Cloudpbx\Sdk\Stirshaken\ApiKey;
use Cloudpbx\Sdk\Stirshaken\Cdr;
use Cloudpbx\Sdk\Stirshaken\Provider;

/**
 * @property Ip $ips
 * @property Certificate $certificates
 * @property ApiKey $apiKeys
 * @property Cdr $cdrs
 * @property Provider $providers
 */
final class Client implements \Cloudpbx\Sdk\Stirshaken\Client
{
    /**
     * @var \Cloudpbx\Sdk\Protocol
     */
    private $protocol;

    /**
     * @param \Cloudpbx\Sdk\Protocol $protocol
     */
    public function __construct($protocol)
    {
        $this->protocol = $protocol;
    }

    /**
     * @return Ip
     */
    public function getIps()
    {
        return Ip::fromTransport($this->protocol);
    }

    /**
     * @return Certificate
     */
    public function getCertificates()
    {
        return Certificate::fromTransport($this->protocol);
    }

    /**
     * @return ApiKey
     */
    public function getApiKeys()
    {
        return ApiKey::fromTransport($this->protocol);
    }

    /**
     * @return Cdr
     */
    public function getCdrs()
    {
        return Cdr::fromTransport($this->protocol);
    }

    /**
     * @return Provider
     */
    public function getProviders()
    {
        return Provider::fromTransport($this->protocol);
    }

    public function __get(string $name): object
    {
        $method = 'get'.ucfirst($name);

        if (method_exists($this, $method)) {
            return $this->$method();
        }

        throw new \RuntimeException('not found API ' . $name);
    }
}

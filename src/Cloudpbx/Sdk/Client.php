<?php

// Copyright 2021 Picallex Holding Group. All rights reserved.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk;

/**
 * @property Customer $customers
 * @property User $users
 * @property CallcenterQueue $callcenterQueues
 * @property RouterDid $routerDids
 * @property FirewallIpSet $firewallIpSets
 */
final class Client
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
     * @return Customer
     */
    public function getCustomers()
    {
        return Customer::fromTransport($this->protocol);
    }

    /**
     * @return User
     */
    public function getUsers()
    {
        return User::fromTransport($this->protocol);
    }

    /**
     * @return CallcenterQueue
     */
    public function getCallcenterQueues()
    {
        return CallcenterQueue::fromTransport($this->protocol);
    }

    /**
     * @return Dialout
     */
    public function getDialouts()
    {
        return Dialout::fromTransport($this->protocol);
    }

    /**
     * @return RouterDid
     */
    public function getRouterDids()
    {
        return RouterDid::fromTransport($this->protocol);
    }

    /**
     * @return FirewallIpSet
     */
    public function getFirewallIpSets()
    {
        return FirewallIpSet::fromTransport($this->protocol);
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

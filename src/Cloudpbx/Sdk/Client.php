<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk;

use Cloudpbx\Util\Inflector;
use Cloudpbx\Util\Argument;

/**
 * @property Customer $customers
 * @property User $users
 * @property CallcenterQueue $callcenterQueues
 * @property RouterDid $routerDids
 * @property FirewallIpSet $firewallIpSets
 * @property Blacklist $blacklists
 * @property Sound $sounds
 * @property CallcenterAgent $callcenterAgents
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
     * @return CallcenterAgent
     */
    public function getCallcenterAgents()
    {
        return CallcenterAgent::fromTransport($this->protocol);
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

    /**
     * @return FollowMe
     */
    public function getFollowMes()
    {
        return FollowMe::fromTransport($this->protocol);
    }

    /**
     * @return IvrMenu
     */
    public function getIvrMenus()
    {
        return IvrMenu::fromTransport($this->protocol);
    }

    /**
     * @return IvrMenuEntry
     */
    public function getIvrMenuEntries()
    {
        return IvrMenuEntry::fromTransport($this->protocol);
    }

    /**
     * @return Blacklist
     */
    public function getBlacklists()
    {
        return Blacklist::fromTransport($this->protocol);
    }

    /**
     * @return FollowMeEntry
     */
    public function getFollowMeEntries()
    {
        return FollowMeEntry::fromTransport($this->protocol);
    }

    /**
     * @return Sound
     */
    public function getSounds()
    {
        return Sound::fromTransport($this->protocol);
    }

    /**
     * @return Supervisor
     */
    public function getSupervisors()
    {
        return Supervisor::fromTransport($this->protocol);
    }

    /**
     * @return CalleridGroup
     */
    public function getCalleridGroups()
    {
        return CalleridGroup::fromTransport($this->protocol);
    }

    /**
     * load relation.
     *
     * @param Model\Relation $relation
     *
     * @return Model
     */
    public function preload($relation)
    {
        # bit4bit: get by convention need a explict contract
        $api_action = 'get' . ucfirst(Inflector::apify($relation->model));
        $api_args = array_merge($relation->path_ids, [$relation->id]);

        # this way allow pass phpstan
        $api = $this->$api_action();
        $call = function (...$params) use ($api) {
            return $api->show(...$params);
        };

        return call_user_func_array($call, $api_args);
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

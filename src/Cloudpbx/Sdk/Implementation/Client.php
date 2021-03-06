<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk\Implementation;

use Cloudpbx\Util\Inflector;
use Cloudpbx\Util\Argument;

use Cloudpbx\Sdk\User;
use Cloudpbx\Sdk\CallcenterQueue;
use Cloudpbx\Sdk\RouterDid;
use Cloudpbx\Sdk\Blacklist;
use Cloudpbx\Sdk\Sound;
use Cloudpbx\Sdk\CallcenterAgent;
use Cloudpbx\Sdk\Voicemail;
use Cloudpbx\Sdk\FollowMeEntry;
use Cloudpbx\Sdk\Group;
use Cloudpbx\Sdk\CalleridGroup;
use Cloudpbx\Sdk\Dialout;
use Cloudpbx\Sdk\DialoutGroup;
use Cloudpbx\Sdk\Callerid;
use Cloudpbx\Sdk\FollowMe;
use Cloudpbx\Sdk\IvrMenu;
use Cloudpbx\Sdk\IvrMenuEntry;
use Cloudpbx\Sdk\Supervisor;
use Cloudpbx\Sdk\AclIpv4;
use Cloudpbx\Sdk\Customer;
use Cloudpbx\Sdk\FirewallIpSet;
use Cloudpbx\Sdk\Model\Relation;

/**
 * @property Customer $customers
 * @property User $users
 * @property CallcenterQueue $callcenterQueues
 * @property RouterDid $routerDids
 * @property FirewallIpSet $firewallIpSets
 * @property Blacklist $blacklists
 * @property Sound $sounds
 * @property CallcenterAgent $callcenterAgents
 * @property Voicemail $voicemails
 * @property DialoutGroup $dialoutGroups
 */
final class Client implements \Cloudpbx\Sdk\Client
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
     * @return DialoutGroup
     */
    public function getDialoutGroups()
    {
        return DialoutGroup::fromTransport($this->protocol);
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
     * @return Voicemail
     */
    public function getVoicemails()
    {
        return Voicemail::fromTransport($this->protocol);
    }

    /**
     * @return Callerid
     */
    public function getCallerids()
    {
        return Callerid::fromTransport($this->protocol);
    }

    /**
     * @return Group
     */
    public function getGroups()
    {
        return Group::fromTransport($this->protocol);
    }

    public function getAclIpv4s()
    {
        return AclIpv4::fromTransport($this->protocol);
    }

    /**
     * load relation.
     *
     * @param Relation $relation
     *
     * @return \Cloudpbx\Sdk\Model
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

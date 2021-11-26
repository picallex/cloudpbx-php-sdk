<?php

// Copyright 2021 Picallex Holding Group. All rights reserved.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk;

interface Client
{
    /**
     * @return Customer
     */
    public function getCustomers();

    /**
     * @return User
     */
    public function getUsers();

    /**
     * @return CallcenterQueue
     */
    public function getCallcenterQueues();

    /**
     * @return CallcenterAgent
     */
    public function getCallcenterAgents();

    /**
     * @return Dialout
     */
    public function getDialouts();

    /**
     * @return RouterDid
     */
    public function getRouterDids();

    /**
     * @return FirewallIpSet
     */
    public function getFirewallIpSets();

    /**
     * @return FollowMe
     */
    public function getFollowMes();

    /**
     * @return IvrMenu
     */
    public function getIvrMenus();

    /**
     * @return IvrMenuEntry
     */
    public function getIvrMenuEntries();

    /**
     * @return Blacklist
     */
    public function getBlacklists();

    /**
     * @return FollowMeEntry
     */
    public function getFollowMeEntries();

    /**
     * @return Sound
     */
    public function getSounds();

    /**
     * @return Supervisor
     */
    public function getSupervisors();

    /**
     * @return CalleridGroup
     */
    public function getCalleridGroups();

    /**
     * @return Voicemail
     */
    public function getVoicemails();

    /**
     * @return Callerid
     */
    public function getCallerids();

    /**
     * @return Group
     */
    public function getGroups();
}

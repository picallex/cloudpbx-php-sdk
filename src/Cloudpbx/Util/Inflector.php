<?php

// Copyright 2021 Picallex Holding Group. All rights reserved.
//
// @author (2021) Jovany Leandro G.C <jovany@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Util;

final class Inflector
{
    /**
     * @param string $word
     *
     * @return string
     */
    public static function apify($word)
    {
        # ATTENTION Temporary coupling with Cloudpbx\Sdk\Client
        $word = strtolower($word);

        $convert = [
            'customer' => 'Customers',
            'user' => 'Users',
            'sound' => 'Sounds',
            'dialout' => 'Dialouts',
            'callcenter_queue' => 'CallcenterQueues',
            'callcenter_agent' => 'CallcenterAgents',
            'ivr_menu' => 'IvrMenus',
            'ivr_menu_entry' => 'IvrMenuEntries',
            'follow_me' => 'FollowMes',
            'follow_me_entry' => 'FollowMeEntries',
            'callerid_group' => 'CalleridGroups'
        ];

        return $convert[$word];
    }
}

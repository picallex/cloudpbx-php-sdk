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
            'dialout' => 'Dialouts',
            'callcenter_queue' => 'CallcenterQueues',
            'ivr_menu' => 'IvrMenus',
            'ivr_menu_entry' => 'IvrMenuEntries',
            'follow_me' => 'FollowMes'
        ];

        return $convert[$word];
    }
}

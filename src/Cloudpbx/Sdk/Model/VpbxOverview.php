<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.
//
// @author (2026) Agustin Serra <agustin@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk\Model;

final class VpbxOverview extends \Cloudpbx\Sdk\Model
{
    /**
     * @var false the overview has no id of its own
     */
    public $id = false;

    protected $_primary_key = null;

    /**
     * @var array<VpbxFreeswitch>
     */
    public $freeswitches = [];

    /**
     * @var array{freeswitches_count: int, customers_count: int, extensions_count: int}
     */
    public $totals = [];

    /**
     * @var array<ProvisioningAttempt>
     */
    public $recent_movements = [];

    public function __construct()
    {
    }

    protected function setup()
    {
        if (is_array($this->freeswitches)) {
            $this->freeswitches = array_map(
                function ($fs) {
                    return VpbxFreeswitch::fromArray($fs);
                },
                $this->freeswitches
            );
        }

        if (is_array($this->recent_movements)) {
            $this->recent_movements = array_map(
                function ($attempt) {
                    return ProvisioningAttempt::fromArray($attempt);
                },
                $this->recent_movements
            );
        }
    }
}

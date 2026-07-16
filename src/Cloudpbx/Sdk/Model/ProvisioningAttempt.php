<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.
//
// @author (2026) Agustin Serra <agustin@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk\Model;

final class ProvisioningAttempt extends \Cloudpbx\Sdk\Model
{
    /**
     * @var integer
     */
    public $customer_id;

    /**
     * @var string|null
     */
    public $domain;

    /**
     * @var integer|null
     */
    public $freeswitch_id;

    /**
     * @var string|null
     */
    public $switchname;

    /**
     * @var string
     */
    public $status;

    /**
     * @var string|null
     */
    public $error_code;

    /**
     * @var string|null
     */
    public $error_message;

    /**
     * @var string|null
     */
    public $origin;

    /**
     * @var string|null
     */
    public $started_at;

    /**
     * @var string|null
     */
    public $finished_at;

    /**
     * @var string
     */
    public $inserted_at;

    /**
     * @var string
     */
    public $updated_at;

    /**
     * Only present on show (detail) responses.
     *
     * @var array<ProvisioningAttemptStep>|null
     */
    public $steps;

    public function __construct()
    {
    }

    protected function setup()
    {
        if (is_array($this->steps)) {
            $this->steps = array_map(
                function ($step) {
                    return ProvisioningAttemptStep::fromArray($step);
                },
                $this->steps
            );
        }
    }
}

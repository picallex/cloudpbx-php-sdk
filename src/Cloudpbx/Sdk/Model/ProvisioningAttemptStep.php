<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.
//
// @author (2026) Agustin Serra <agustin@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk\Model;

final class ProvisioningAttemptStep extends \Cloudpbx\Sdk\Model
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var integer
     */
    public $position;

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
     * @var array<string,mixed>|null
     */
    public $details;

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

    public function __construct()
    {
    }
}

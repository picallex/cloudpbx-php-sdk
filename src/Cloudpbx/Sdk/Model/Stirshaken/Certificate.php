<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.
//
// @author (2026) Agustin Serra <agustin@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk\Model\Stirshaken;

final class Certificate extends \Cloudpbx\Sdk\Model
{
    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string|null
     */
    public $x5u_url;

    /**
     * @var string A|B|C
     */
    public $attest_level;

    /**
     * @var bool
     */
    public $is_primary;

    /**
     * @var bool
     */
    public $is_active;

    /**
     * @var string|null ISO-8601
     */
    public $not_before;

    /**
     * @var string|null ISO-8601
     */
    public $not_after;

    /**
     * @var string|null ISO-8601
     */
    public $created_at;

    public function __construct()
    {
    }
}

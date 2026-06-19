<?php

// This file is part of cloudpbx-php-sdk.  The COPYRIGHT file at the top level of
// this repository contains the full copyright notices and license terms.
//
// @author (2026) Agustin Serra <agustin@picallex.com>

declare(strict_types=1);

namespace Cloudpbx\Sdk\Model\Webphone;

final class ExtensionAvailability extends \Cloudpbx\Sdk\Model
{
    protected $_primary_key = null;

    /**
     * @var string
     */
    public $domain;

    /**
     * @var string
     */
    public $extension;

    /**
     * @var string
     */
    public $fqdn;

    /**
     * @var bool
     */
    public $registered;

    /**
     * @var bool
     */
    public $transferable;

    public function __construct()
    {
    }
}
